<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private function getCart()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->first();
        } else {
            return Cart::where('session_id', session()->getId())->first();
        }
    }

    public function index()
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'السلة فارغة');
        }

        $cart->load('items.product');

        return view('checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_zip' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cod,stripe,paypal',
        ]);

        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'السلة فارغة');
        }

        // Check stock availability
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with('error', "المنتج {$item->product->name} غير متوفر بالكمية المطلوبة");
            }
        }

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            foreach ($cart->items as $item) {
                $subtotal += $item->product->getCurrentPrice() * $item->quantity;
            }

            $discount = session('coupon_discount', 0);
            $couponId = session('coupon_id');

            $shippingCost = 0; // يمكن تعديله حسب الحاجة
            $tax = 0; // يمكن تعديله حسب الحاجة
            $total = $subtotal - $discount + $shippingCost + $tax;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'pending',
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'] ?? null,
                'shipping_zip' => $validated['shipping_zip'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'coupon_id' => $couponId,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total' => $total,
            ]);

            // Create order items and update stock
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->getCurrentPrice(),
                    'quantity' => $item->quantity,
                    'total' => $item->product->getCurrentPrice() * $item->quantity,
                ]);

                // Update stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Update coupon usage
            if ($couponId) {
                $coupon = Coupon::find($couponId);
                if ($coupon) {
                    $coupon->increment('used_count');
                }
            }

            // Clear cart
            $cart->items()->delete();

            // Clear coupon session
            session()->forget(['coupon_code', 'coupon_id', 'coupon_discount']);

            DB::commit();

            // Handle payment methods
            if ($validated['payment_method'] === 'cod') {
                return redirect()->route('checkout.success', $order)->with('success', 'تم إنشاء الطلب بنجاح! سيتم التواصل معك قريباً');
            } elseif ($validated['payment_method'] === 'stripe') {
                // Redirect to Stripe payment (to be implemented)
                return redirect()->route('payment.stripe', $order);
            } elseif ($validated['payment_method'] === 'paypal') {
                // Redirect to PayPal payment (to be implemented)
                return redirect()->route('payment.paypal', $order);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الطلب. الرجاء المحاولة مرة أخرى.');
        }
    }

    public function success(Order $order)
    {
        return view('checkout.success', compact('order'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))
            ->where('is_active', true)
            ->first();

        if (!$coupon || !$coupon->isValid()) {
            return back()->with('coupon_error', 'الكوبون غير صحيح أو منتهي الصلاحية');
        }

        $cart = $this->getCart();
        if (!$cart) {
            return back()->with('coupon_error', 'السلة فارغة');
        }

        $subtotal = $cart->getTotal();

        if ($subtotal < $coupon->min_purchase) {
            return back()->with('coupon_error', "الحد الأدنى للشراء هو " . number_format((float)$coupon->min_purchase, 0));
        }

        $discount = $coupon->calculateDiscount($subtotal);

        session([
            'coupon_code' => $coupon->code,
            'coupon_id' => $coupon->id,
            'coupon_discount' => $discount
        ]);

        return back()->with('success', 'تم تطبيق الكوبون بنجاح');
    }

    public function removeCoupon()
    {
        session()->forget(['coupon_code', 'coupon_id', 'coupon_discount']);
        return back()->with('success', 'تم إزالة الكوبون');
    }
}
