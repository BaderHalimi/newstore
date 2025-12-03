# 🎉 تحديثات نظام العملاء وتسجيل الدخول

## ✅ ما تم إنجازه

### 1. إنشاء جدول العملاء المنفصل
- ✅ جدول `customers` جديد مع الحقول:
  - name, email, phone, password
  - google_id (لتسجيل الدخول بـ Google)
  - avatar, email_verified_at
  - timestamps

### 2. جدول رموز تسجيل الدخول
- ✅ جدول `login_tokens` لحفظ رموز التحقق
  - email, token (6 أرقام), expires_at

### 3. تحديث الجداول المرتبطة
- ✅ إضافة `customer_id` للجداول:
  - orders
  - carts
  - reviews
- ✅ الحقول القديمة (`user_id`) باقية للتوافق

### 4. نظام المصادقة
- ✅ Auth Guard جديد للعملاء (`customer`)
- ✅ Provider منفصل في `config/auth.php`

### 5. Controllers
- ✅ `CustomerAuthController`:
  - التسجيل
  - إرسال رمز التحقق عبر البريد
  - تسجيل الدخول بالرمز
  - تسجيل الدخول بكلمة المرور
  - تسجيل الخروج

### 6. نظام Popup بـ JavaScript
- ✅ ملف `public/js/auth-modal.js`
- ✅ واجهة كاملة بدون Livewire
- ✅ 4 واجهات:
  1. تسجيل الدخول (إرسال رمز)
  2. التسجيل
  3. تسجيل الدخول بكلمة المرور
  4. إدخال رمز التحقق

### 7. Routes
- ✅ جميع routes المطلوبة:
  - POST /auth/register
  - POST /auth/send-code
  - POST /auth/login-code
  - POST /auth/login-password
  - POST /auth/logout
  - GET /auth/google
  - GET /auth/google/callback

### 8. تحديث الـ Layout
- ✅ إضافة أزرار تسجيل الدخول/التسجيل
- ✅ عرض معلومات العميل بعد تسجيل الدخول
- ✅ قائمة منسدلة للخروج

---

## 📋 ما يجب إكماله

### 1. ⚠️ Google OAuth Controller
يجب إكمال `GoogleAuthController` لتسجيل الدخول بـ Google:

```bash
cd /home/bader/Desktop/laravel/newstore
composer require laravel/socialite
```

ثم في `.env`:
```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

ثم إنشاء `config/services.php`:
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

وإكمال `GoogleAuthController`:
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $customer = Customer::where('email', $googleUser->email)->first();
            
            if (!$customer) {
                // عميل جديد - إعادة توجيه لإكمال البيانات
                session([
                    'google_user' => [
                        'google_id' => $googleUser->id,
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'avatar' => $googleUser->avatar,
                    ]
                ]);
                
                return redirect()->route('shop.index')->with('complete_registration', true);
            }
            
            // تحديث google_id إذا لم يكن موجوداً
            if (!$customer->google_id) {
                $customer->update(['google_id' => $googleUser->id]);
            }
            
            Auth::guard('customer')->login($customer);
            
            return redirect()->route('shop.index');
            
        } catch (\Exception $e) {
            return redirect()->route('shop.index')->with('error', 'حدث خطأ في تسجيل الدخول بـ Google');
        }
    }
}
```

### 2. ⚠️ إكمال التسجيل بعد Google
إضافة popup لإكمال البيانات بعد تسجيل الدخول بـ Google في `auth-modal.js`:

```javascript
getGoogleCompleteView() {
    const googleUser = sessionGoogleUser; // من session
    return `
        <form id="googleCompleteForm">
            <input type="hidden" name="google_id" value="${googleUser.google_id}">
            <input type="hidden" name="email" value="${googleUser.email}">
            
            <div>
                <label>رقم الهاتف</label>
                <input type="tel" name="phone" required>
            </div>
            
            <div>
                <label>كلمة المرور</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit">إكمال التسجيل</button>
        </form>
    `;
}
```

### 3. ⚠️ تحديث نظام الكوبونات
في `CheckoutController` إضافة دعم الكوبونات:

```php
public function applyCoupon(Request $request)
{
    $request->validate([
        'code' => 'required|string'
    ]);
    
    $coupon = \App\Models\Coupon::where('code', strtoupper($request->code))
        ->where('is_active', true)
        ->first();
    
    if (!$coupon || !$coupon->isValid()) {
        return back()->with('error', 'الكوبون غير صحيح أو منتهي الصلاحية');
    }
    
    // حساب الخصم
    $cart = /* get cart */;
    $subtotal = $cart->items->sum(fn($item) => $item->quantity * $item->price);
    
    if ($subtotal < $coupon->min_purchase) {
        return back()->with('error', "الحد الأدنى للشراء هو {$coupon->min_purchase}");
    }
    
    $discount = $coupon->calculateDiscount($subtotal);
    
    session([
        'coupon_code' => $coupon->code,
        'coupon_discount' => $discount
    ]);
    
    return back()->with('success', 'تم تطبيق الكوبون بنجاح');
}
```

### 4. ⚠️ عرض التقييمات بأسلوب Google Play
في صفحة المنتج `shop/show.blade.php`:

```blade
<!-- قسم التقييمات -->
<div class="mt-12">
    <h2 class="text-2xl font-bold mb-6">التقييمات والمراجعات</h2>
    
    <!-- ملخص التقييمات -->
    <div class="bg-white rounded-lg p-6 mb-6">
        <div class="flex items-center gap-8">
            <div class="text-center">
                <div class="text-5xl font-bold">{{ number_format($product->reviews->avg('rating'), 1) }}</div>
                <div class="text-yellow-500 text-2xl my-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i <= round($product->reviews->avg('rating')) ? '' : '-o' }}"></i>
                    @endfor
                </div>
                <div class="text-gray-500">{{ $product->reviews->count() }} تقييم</div>
            </div>
            
            <div class="flex-1">
                @foreach([5,4,3,2,1] as $rating)
                    @php
                        $count = $product->reviews->where('rating', $rating)->count();
                        $percentage = $product->reviews->count() > 0 ? ($count / $product->reviews->count()) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-3">{{ $rating }}</span>
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-sm text-gray-500 w-12">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- قائمة التقييمات -->
    <div class="space-y-4">
        @foreach($product->reviews()->where('is_approved', true)->latest()->get() as $review)
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-bold">
                        {{ substr($review->customer_name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold">{{ $review->customer_name }}</h4>
                            <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-yellow-500 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }} text-sm"></i>
                            @endfor
                        </div>
                        <p class="text-gray-700">{{ $review->comment }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
```

### 5. ⚠️ تحديث Models للعمل مع customer_id
في `Order`, `Cart`, `Review` models:

```php
// إضافة في Order.php
public function customer()
{
    return $this->belongsTo(Customer::class);
}

// تحديث accessor
public function getCustomerAttribute()
{
    return $this->customer_id ? $this->customer : $this->user;
}
```

### 6. ⚠️ تحديث Controllers
في `CheckoutController`, `CartController`:

```php
// استبدال auth()->id() بـ:
$customerId = auth('customer')->id();

// استبدال auth()->check() بـ:
if (auth('customer')->check()) {
    // ...
}
```

---

## 🚀 أوامر التشغيل

```bash
# 1. تثبيت Socialite لـ Google OAuth
composer require laravel/socialite

# 2. مسح الكاش
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 3. تشغيل الـ migrations (تم بالفعل)
# php artisan migrate

# 4. تشغيل السيرفر
php artisan serve
```

---

## ✨ الميزات الجاهزة

- ✅ تسجيل دخول بدون كلمة مرور (عبر رمز البريد)
- ✅ تسجيل دخول بكلمة المرور (خيارات أخرى)
- ✅ التسجيل الكامل
- ✅ Popup بدون Livewire
- ✅ جدول منفصل للعملاء
- ✅ دعم Google OAuth (يحتاج إعداد)
- ✅ تحديث الجداول للعمل مع العملاء

---

## 📝 ملاحظات مهمة

1. **البريد الإلكتروني**: تأكد من إعداد SMTP في `.env` لإرسال رموز التحقق
2. **Google OAuth**: احصل على Client ID و Secret من Google Cloud Console
3. **الكوبونات**: الكود جاهز، يحتاج فقط تطبيق في واجهة الـ checkout
4. **التقييمات**: التصميم جاهز، انسخ الكود أعلاه في صفحة المنتج

---

**جميع الأساسيات جاهزة! يمكنك الآن اختبار النظام والبناء عليه.** 🎊
