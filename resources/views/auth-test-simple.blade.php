<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>اختبار التسجيل</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-xl p-8">
        <h1 class="text-3xl font-bold text-center mb-8 text-purple-600">اختبار نظام التسجيل</h1>

        <div class="space-y-4 mb-8">
            <button onclick="authModal.open('register')" class="w-full bg-purple-600 text-white py-4 rounded-lg hover:bg-purple-700 transition text-lg font-semibold">
                📝 إنشاء حساب جديد
            </button>

            <button onclick="authModal.open('login')" class="w-full bg-green-600 text-white py-4 rounded-lg hover:bg-green-700 transition text-lg font-semibold">
                🔐 تسجيل دخول
            </button>
        </div>

        @auth('customer')
            <div class="bg-green-50 border-2 border-green-500 rounded-lg p-4">
                <p class="text-green-700 font-bold mb-2">✓ مسجل دخول</p>
                <p class="text-sm text-gray-600 mb-1">الاسم: {{ auth('customer')->user()->name }}</p>
                <p class="text-sm text-gray-600 mb-3">البريد: {{ auth('customer')->user()->email }}</p>
                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                        تسجيل خروج
                    </button>
                </form>
            </div>
        @else
            <div class="bg-yellow-50 border-2 border-yellow-500 rounded-lg p-4">
                <p class="text-yellow-700 font-bold">⚠ غير مسجل دخول</p>
            </div>
        @endauth

        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm font-bold text-blue-800 mb-2">📧 فحص البريد:</p>
            <a href="http://localhost:8025" target="_blank" class="text-blue-600 hover:underline text-sm">
                افتح Mailpit للتحقق من OTP
            </a>
        </div>

        <div class="mt-4 text-xs text-gray-500 text-center">
            <p>تأكد من فتح Console للتحقق من الأخطاء (F12)</p>
        </div>
    </div>

    <script src="{{ asset('js/auth-modal.js') }}"></script>
    <script>
        console.log('✓ CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
        console.log('✓ Auth Modal loaded:', typeof authModal !== 'undefined');
    </script>
</body>
</html>
