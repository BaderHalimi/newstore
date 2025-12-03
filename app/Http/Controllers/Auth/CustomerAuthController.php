<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CustomerAuthController extends Controller
{
    // التسجيل
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            // لا نفعل الحساب مباشرة - ننتظر التحقق
        ]);

        // توليد رمز OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // حفظ الرمز
        DB::table('login_tokens')->insert([
            'email' => $customer->email,
            'token' => $otp,
            'expires_at' => now()->addMinutes(15), // صلاحية 15 دقيقة
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إرسال البريد
        try {
            $storeName = \App\Models\Setting::get('store_name', config('app.name'));
            $logo = \App\Models\Setting::get('store_logo');

            Mail::send('emails.otp-verification', [
                'customerName' => $customer->name,
                'otp' => $otp,
                'storeName' => $storeName,
                'logo' => $logo
            ], function ($message) use ($customer, $storeName) {
                $message->to($customer->email)
                    ->subject($storeName . ' - تفعيل حسابك');
            });
        } catch (\Exception $e) {
            // في حال فشل الإرسال، نسجل خطأ لكن نكمل
            \Log::error('فشل إرسال بريد التفعيل: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'تم التسجيل بنجاح! تحقق من بريدك الإلكتروني لتفعيل حسابك',
            'requiresVerification' => true,
            'email' => $customer->email
        ]);
    }

    // تفعيل الحساب بالـ OTP
    public function verifyAccount(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $token = DB::table('login_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التفعيل غير صحيح أو منتهي الصلاحية'
            ], 422);
        }

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'الحساب غير موجود'
            ], 404);
        }

        // تفعيل الحساب
        $customer->update(['email_verified_at' => now()]);

        // حذف الرمز
        DB::table('login_tokens')->where('id', $token->id)->delete();

        // تسجيل دخول تلقائي
        Auth::guard('customer')->login($customer);

        return response()->json([
            'success' => true,
            'message' => 'تم تفعيل حسابك بنجاح!',
            'redirect' => route('shop.index')
        ]);
    }

    // إعادة إرسال رمز التفعيل
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email'
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if ($customer->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'الحساب مفعّل بالفعل'
            ], 422);
        }

        // حذف الرموز القديمة
        DB::table('login_tokens')->where('email', $request->email)->delete();

        // توليد رمز جديد
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('login_tokens')->insert([
            'email' => $customer->email,
            'token' => $otp,
            'expires_at' => now()->addMinutes(15),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $storeName = \App\Models\Setting::get('store_name', config('app.name'));
            $logo = \App\Models\Setting::get('store_logo');

            Mail::send('emails.otp-verification', [
                'customerName' => $customer->name,
                'otp' => $otp,
                'storeName' => $storeName,
                'logo' => $logo
            ], function ($message) use ($customer, $storeName) {
                $message->to($customer->email)
                    ->subject($storeName . ' - رمز التفعيل الجديد');
            });

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال رمز التفعيل إلى بريدك الإلكتروني'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إرسال البريد'
            ], 500);
        }
    }

    // إرسال رمز تسجيل الدخول عبر البريد
    public function sendLoginCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email'
        ]);

        // حذف الرموز القديمة
        DB::table('login_tokens')
            ->where('email', $request->email)
            ->where('expires_at', '<', now())
            ->delete();

        // توليد رمز عشوائي من 6 أرقام
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // حفظ الرمز
        DB::table('login_tokens')->insert([
            'email' => $request->email,
            'token' => $code,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إرسال البريد
        try {
            $customer = Customer::where('email', $request->email)->first();
            $storeName = \App\Models\Setting::get('store_name', config('app.name'));
            $logo = \App\Models\Setting::get('store_logo');

            Mail::send('emails.otp-verification', [
                'customerName' => $customer->name,
                'otp' => $code,
                'storeName' => $storeName,
                'logo' => $logo
            ], function ($message) use ($request, $storeName) {
                $message->to($request->email)
                    ->subject($storeName . ' - رمز تسجيل الدخول');
            });

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إرسال البريد. استخدم كلمة المرور بدلاً من ذلك'
            ], 500);
        }
    }

    // تسجيل الدخول بالرمز
    public function loginWithCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        $token = DB::table('login_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'الرمز غير صحيح أو منتهي الصلاحية'
            ], 422);
        }

        $customer = Customer::where('email', $request->email)->first();

        Auth::guard('customer')->login($customer);

        // حذف الرمز بعد الاستخدام
        DB::table('login_tokens')->where('id', $token->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح!',
            'redirect' => route('shop.index')
        ]);
    }

    // تسجيل الدخول بكلمة المرور
    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ], 422);
        }

        Auth::guard('customer')->login($customer, $request->filled('remember'));

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح!',
            'redirect' => route('shop.index')
        ]);
    }

    // تسجيل الخروج
    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect()->route('shop.index');
    }
}
