<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getCart()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);
        } else {
            $sessionId = session()->getId();
            $cart = Cart::firstOrCreate([
                'session_id' => $sessionId
            ]);
        }

        return $cart;
    }

    public function index()
    {
        $cart = $this->getCart();
        $cart->load('items.product');

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'الكمية المطلوبة غير متوفرة في المخزون');
        }

        $cart = $this->getCart();

        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($product->stock < $newQuantity) {
                return back()->with('error', 'الكمية المطلوبة غير متوفرة في المخزون');
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'تمت إضافة المنتج إلى السلة بنجاح');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if ($cartItem->product->stock < $request->quantity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'الكمية المطلوبة غير متوفرة في المخزون'
                ], 422);
            }
            return back()->with('error', 'الكمية المطلوبة غير متوفرة في المخزون');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        if ($request->expectsJson()) {
            $cart = $this->getCart();
            $cart->load('items.product');

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث السلة بنجاح',
                'itemSubtotal' => $cartItem->getSubtotal(),
                'cartTotal' => $cart->getTotal(),
                'cartItemsCount' => $cart->items->sum('quantity')
            ]);
        }

        return back()->with('success', 'تم تحديث السلة بنجاح');
    }

    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج من السلة'
            ]);
        }

        return back()->with('success', 'تم حذف المنتج من السلة');
    }

    public function clear()
    {
        $cart = $this->getCart();
        $cart->items()->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تفريغ السلة بنجاح'
            ]);
        }

        return back()->with('success', 'تم تفريغ السلة بنجاح');
    }
}
