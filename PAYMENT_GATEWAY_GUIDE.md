# دليل تفعيل بوابات الدفع الإلكتروني

## نظام الدفع الحالي

المتجر حالياً مهيأ للعمل بـ:
- ✅ **الدفع عند الاستلام (COD)** - مفعّل ويعمل
- 🔄 **Stripe** - جاهز للتفعيل
- 🔄 **PayPal** - جاهز للتفعيل

## 🟢 الدفع عند الاستلام (Cash on Delivery)

### الحالة: مفعّل ✅

هذا هو الخيار الافتراضي ويعمل مباشرة. عند اختيار العميل لهذا الخيار:
1. يتم إنشاء الطلب بحالة `pending`
2. حالة الدفع تكون `pending`
3. يتم خصم الكمية من المخزون
4. يتم إرسال تأكيد للعميل

### إدارة الطلبات
من لوحة تحكم Filament:
- غيّر حالة الطلب حسب التقدم
- قم بتحديث حالة الدفع عند الاستلام

---

## 💳 Stripe Payment Gateway

### الميزات
- قبول بطاقات الائتمان العالمية
- أمان عالي (PCI Compliant)
- رسوم منخفضة نسبياً
- دعم عدة عملات

### خطوات التفعيل

#### 1. إنشاء حساب Stripe
1. انتقل إلى https://stripe.com
2. سجل حساب جديد
3. أكمل التحقق من الهوية

#### 2. الحصول على API Keys
1. من Dashboard، اذهب إلى **Developers** > **API keys**
2. احفظ:
   - **Publishable key**: `pk_test_...` (للاختبار)
   - **Secret key**: `sk_test_...` (للاختبار)

#### 3. تثبيت المكتبة
```bash
composer require stripe/stripe-php
```

#### 4. إضافة المفاتيح في .env
```env
STRIPE_KEY=pk_test_51...
STRIPE_SECRET=sk_test_51...
STRIPE_WEBHOOK_SECRET=whsec_...
```

#### 5. تفعيل الزر في صفحة Checkout

في `/resources/views/checkout/index.blade.php`، ابحث عن:
```html
<label class="... opacity-50 cursor-not-allowed">
    <input type="radio" name="payment_method" value="stripe" disabled class="ml-3">
```

غيّرها إلى:
```html
<label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition">
    <input type="radio" name="payment_method" value="stripe" class="ml-3">
```

#### 6. إنشاء Controller للدفع

أنشئ ملف `app/Http/Controllers/StripeController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function payment(Order $order)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Order #' . $order->order_number,
                    ],
                    'unit_amount' => $order->total * 100, // Convert to cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.stripe.success', $order),
            'cancel_url' => route('payment.stripe.cancel', $order),
            'client_reference_id' => $order->id,
        ]);

        return redirect($session->url);
    }

    public function success(Order $order)
    {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        return redirect()->route('checkout.success', $order);
    }

    public function cancel(Order $order)
    {
        return redirect()->route('checkout.index')
            ->with('error', 'تم إلغاء عملية الدفع');
    }
}
```

#### 7. إضافة Routes

في `routes/web.php`:
```php
Route::get('/payment/stripe/{order}', [StripeController::class, 'payment'])->name('payment.stripe');
Route::get('/payment/stripe/success/{order}', [StripeController::class, 'success'])->name('payment.stripe.success');
Route::get('/payment/stripe/cancel/{order}', [StripeController::class, 'cancel'])->name('payment.stripe.cancel');
```

#### 8. إضافة Config

في `config/services.php`:
```php
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
],
```

#### 9. الاختبار

بطاقات اختبار Stripe:
- **نجاح**: `4242 4242 4242 4242`
- **فشل**: `4000 0000 0000 0002`
- التاريخ: أي تاريخ مستقبلي
- CVV: أي 3 أرقام

#### 10. النشر الحقيقي
عند الاستعداد للنشر:
1. غيّر المفاتيح إلى Live keys (`pk_live_...` و `sk_live_...`)
2. غيّر العملة حسب حاجتك
3. أكمل التحقق من الحساب في Stripe

---

## 🔵 PayPal Payment Gateway

### الميزات
- معروف عالمياً
- يدعم حسابات PayPal والبطاقات
- سهل الاستخدام

### خطوات التفعيل

#### 1. إنشاء حساب PayPal Developer
1. انتقل إلى https://developer.paypal.com
2. سجل دخول أو أنشئ حساب

#### 2. إنشاء App
1. من **Dashboard**، اذهب إلى **My Apps & Credentials**
2. اضغط **Create App**
3. احصل على:
   - **Client ID**
   - **Secret**

#### 3. تثبيت المكتبة
```bash
composer require paypal/rest-api-sdk-php
```

أو استخدم PayPal Checkout:
```bash
composer require srmklive/paypal
```

#### 4. إضافة المفاتيح في .env
```env
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=...
PAYPAL_SANDBOX_SECRET=...
PAYPAL_LIVE_CLIENT_ID=...
PAYPAL_LIVE_SECRET=...
```

#### 5. تفعيل الزر في صفحة Checkout

نفس الخطوات السابقة، احذف `disabled` و `opacity-50 cursor-not-allowed`

#### 6. إنشاء Controller للدفع

أنشئ ملف `app/Http/Controllers/PayPalController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function payment(Order $order)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => $order->total
                ]
            ]],
            'application_context' => [
                'return_url' => route('payment.paypal.success', $order),
                'cancel_url' => route('payment.paypal.cancel', $order),
            ]
        ]);

        if (isset($response['id'])) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect($link['href']);
                }
            }
        }

        return redirect()->route('checkout.index')
            ->with('error', 'حدث خطأ في PayPal');
    }

    public function success(Request $request, Order $order)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);

        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);

            return redirect()->route('checkout.success', $order);
        }

        return redirect()->route('checkout.index')
            ->with('error', 'فشلت عملية الدفع');
    }

    public function cancel(Order $order)
    {
        return redirect()->route('checkout.index')
            ->with('error', 'تم إلغاء عملية الدفع');
    }
}
```

#### 7. إضافة Routes

في `routes/web.php`:
```php
Route::get('/payment/paypal/{order}', [PayPalController::class, 'payment'])->name('payment.paypal');
Route::get('/payment/paypal/success/{order}', [PayPalController::class, 'success'])->name('payment.paypal.success');
Route::get('/payment/paypal/cancel/{order}', [PayPalController::class, 'cancel'])->name('payment.paypal.cancel');
```

#### 8. إضافة Config

أنشئ `config/paypal.php`:
```php
<?php

return [
    'mode' => env('PAYPAL_MODE', 'sandbox'),
    'sandbox' => [
        'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_SANDBOX_SECRET', ''),
        'app_id' => '',
    ],
    'live' => [
        'client_id' => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_LIVE_SECRET', ''),
        'app_id' => '',
    ],
    'payment_action' => 'Sale',
    'currency' => 'USD',
    'notify_url' => '',
    'locale' => 'en_US',
    'validate_ssl' => true,
];
```

#### 9. الاختبار

استخدم حسابات PayPal Sandbox من Developer Dashboard

#### 10. النشر الحقيقي
1. غيّر `PAYPAL_MODE=live`
2. استخدم Live credentials
3. أكمل التحقق من حسابك

---

## 🔒 الأمان

### نصائح مهمة
1. ✅ لا تخزن معلومات البطاقات أبداً
2. ✅ استخدم HTTPS في الإنتاج
3. ✅ احفظ API Keys في `.env` فقط
4. ✅ فعّل Webhooks للتحقق من الدفع
5. ✅ سجّل جميع المعاملات

### Webhooks

#### Stripe Webhooks
1. في Stripe Dashboard > Developers > Webhooks
2. أضف endpoint: `https://yourdomain.com/webhook/stripe`
3. اختر Events: `payment_intent.succeeded`

#### PayPal Webhooks
1. في PayPal Dashboard > Webhooks
2. أضف URL: `https://yourdomain.com/webhook/paypal`
3. اختر Events: `PAYMENT.CAPTURE.COMPLETED`

---

## 📊 اختبار الدفع

### خطوات الاختبار
1. أضف منتج للسلة
2. اذهب إلى Checkout
3. املأ معلومات التوصيل
4. اختر طريقة الدفع
5. أكمل الدفع (استخدم بيانات اختبار)
6. تحقق من تحديث حالة الطلب

---

## ❓ الأسئلة الشائعة

**س: ما هي أفضل بوابة دفع؟**
ج: يعتمد على موقعك:
- Stripe: أفضل عالمياً
- PayPal: معروف ومألوف
- COD: الأفضل محلياً في سوريا

**س: هل يمكن استخدام أكثر من بوابة؟**
ج: نعم! يمكن تفعيل جميع الخيارات معاً

**س: ما هي الرسوم؟**
ج: 
- COD: لا رسوم (فقط مصاريف التوصيل)
- Stripe: ~2.9% + $0.30 لكل عملية
- PayPal: ~3.4% + رسوم ثابتة

---

**ملاحظة**: الكود الحالي جاهز، تحتاج فقط تفعيل البوابة المطلوبة!
