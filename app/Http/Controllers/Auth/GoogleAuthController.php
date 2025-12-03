<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        try {
            // التحقق من أن الإعدادات موجودة
            $clientId = Setting::get('google_client_id');
            $clientSecret = Setting::get('google_client_secret');
            $redirectUrl = Setting::get('google_redirect_url', url('/auth/google/callback'));

            if (!$clientId || !$clientSecret) {
                return redirect()->route('shop.index')
                    ->with('error', 'تسجيل الدخول عبر Google غير متاح حالياً');
            }

            // تعيين الإعدادات ديناميكياً
            config([
                'services.google.client_id' => $clientId,
                'services.google.client_secret' => $clientSecret,
                'services.google.redirect' => $redirectUrl,
            ]);

            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            return redirect()->route('shop.index')
                ->with('error', 'حدث خطأ في تسجيل الدخول عبر Google');
        }
    }

    public function callback()
    {
        try {
            // تعيين الإعدادات ديناميكياً
            $clientId = Setting::get('google_client_id');
            $clientSecret = Setting::get('google_client_secret');
            $redirectUrl = Setting::get('google_redirect_url', url('/auth/google/callback'));

            config([
                'services.google.client_id' => $clientId,
                'services.google.client_secret' => $clientSecret,
                'services.google.redirect' => $redirectUrl,
            ]);

            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();

            // البحث عن العميل بالبريد الإلكتروني
            $customer = Customer::where('email', $googleUser->email)->first();

            if ($customer) {
                // إذا كان العميل موجود، نقوم بتسجيل دخوله
                if (!$customer->is_verified) {
                    $customer->update(['is_verified' => true]);
                }

                // تحديث google_id إذا لم يكن موجود
                if (!$customer->google_id) {
                    $customer->update(['google_id' => $googleUser->id]);
                }
            } else {
                // إنشاء عميل جديد
                $customer = Customer::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'is_verified' => true, // التحقق تلقائياً لأنه من Google
                ]);
            }

            // تسجيل الدخول
            Auth::guard('customer')->login($customer);

            return redirect()->route('shop.index')
                ->with('success', 'تم تسجيل الدخول بنجاح!');

        } catch (\Exception $e) {
            return redirect()->route('shop.index')
                ->with('error', 'حدث خطأ في تسجيل الدخول عبر Google');
        }
    }
}
