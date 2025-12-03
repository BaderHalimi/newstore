<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>اختبار نظام التسجيل</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-center mb-6">اختبار نظام التسجيل</h1>

            <!-- زر فتح نافذة التسجيل -->
            <button onclick="authModal.open('register')" class="w-full bg-blue-600 text-white py-3 rounded-lg mb-4 hover:bg-blue-700">
                إنشاء حساب جديد
            </button>

            <!-- زر فتح نافذة تسجيل الدخول -->
            <button onclick="authModal.open('login')" class="w-full bg-green-600 text-white py-3 rounded-lg mb-4 hover:bg-green-700">
                تسجيل الدخول
            </button>

            <!-- عرض الحالة -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                @auth('customer')
                    <p class="text-green-600 font-semibold">✓ أنت مسجل دخول</p>
                    <p class="text-sm text-gray-600 mt-2">الاسم: {{ auth('customer')->user()->name }}</p>
                    <p class="text-sm text-gray-600">البريد: {{ auth('customer')->user()->email }}</p>
                    <form action="{{ route('auth.logout') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                            تسجيل الخروج
                        </button>
                    </form>
                @else
                    <p class="text-gray-600">لم تسجل دخول بعد</p>
                @endauth
            </div>

            <!-- معلومات الاختبار -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="font-bold text-blue-800 mb-2">تعليمات الاختبار:</h3>
                <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                    <li>انقر على "إنشاء حساب جديد"</li>
                    <li>أدخل البيانات المطلوبة</li>
                    <li>افتح Mailpit على <code class="bg-blue-100 px-1 rounded">localhost:8025</code></li>
                    <li>ابحث عن البريد الإلكتروني وانسخ رمز OTP</li>
                    <li>أدخل الرمز في نافذة التحقق</li>
                </ol>
            </div>

            <!-- رابط Mailpit -->
            <div class="mt-4 text-center">
                <a href="http://localhost:8025" target="_blank" class="text-blue-600 hover:underline">
                    🔗 فتح Mailpit
                </a>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/auth-modal.js') }}"></script>
</body>
</html>
