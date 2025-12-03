@php
    use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $store_name ?? 'متجر مستحضرات التجميل')</title>

    @if($store_favicon)
        <link rel="icon" type="image/png" href="{{ Storage::url($store_favicon) }}">
        <link rel="shortcut icon" type="image/png" href="{{ Storage::url($store_favicon) }}">
        <link rel="apple-touch-icon" href="{{ Storage::url($store_favicon) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap');

        body {
            font-family: 'Tajawal', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hover-scale {
            transition: transform 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        .cart-badge {
            animation: bounce 0.5s ease;
        }

        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ route('shop.index') }}" class="flex items-center space-x-2 space-x-reverse">
                    @if($store_logo)
                        <img src="{{ Storage::url($store_logo) }}" alt="{{ $store_name ?? 'متجر الجمال' }}" class="h-12 w-auto object-contain">
                    @else
                        <div class="gradient-bg w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="fas fa-spa text-white text-xl"></i>
                        </div>
                    @endif
                    <span class="text-2xl font-bold gradient-text">{{ $store_name ?? 'متجر الجمال' }}</span>
                </a>

                <!-- Search Bar (Desktop) -->
                <div class="hidden md:flex flex-1 max-w-md mx-8">
                    <div class="relative w-full">
                        <input type="text" placeholder="ابحث عن منتجاتك المفضلة..."
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <button class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center space-x-6 space-x-reverse">
                    <a href="{{ route('shop.index') }}" class="text-gray-700 hover:text-purple-600 transition">
                        <i class="fas fa-home ml-1"></i>
                        الرئيسية
                    </a>

                    @auth
                        <a href="{{ route('account.orders') }}" class="text-gray-700 hover:text-purple-600 transition">
                            <i class="fas fa-box ml-1"></i>
                            طلباتي
                        </a>
                    @endauth

                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-purple-600 transition">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                        @php
                            $cart = null;
                            if (auth()->check()) {
                                $cart = \App\Models\Cart::where('user_id', auth()->id())->first();
                            } else {
                                $cart = \App\Models\Cart::where('session_id', session()->getId())->first();
                            }
                            $cartCount = $cart ? $cart->items->sum('quantity') : 0;
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -left-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center cart-badge">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">متجر الجمال</h3>
                    <p class="text-gray-400">متجرك المفضل لجميع مستحضرات التجميل الأصلية</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">روابط سريعة</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('shop.index') }}" class="hover:text-white transition">الرئيسية</a></li>
                        <li><a href="#" class="hover:text-white transition">من نحن</a></li>
                        <li><a href="#" class="hover:text-white transition">اتصل بنا</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">خدمة العملاء</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">سياسة الاسترجاع</a></li>
                        <li><a href="#" class="hover:text-white transition">الشحن والتوصيل</a></li>
                        <li><a href="#" class="hover:text-white transition">الأسئلة الشائعة</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">تواصل معنا</h4>
                    <div class="flex space-x-4 space-x-reverse">
                        <a href="#" class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center hover:bg-purple-700 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center hover:bg-purple-700 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center hover:bg-purple-700 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} متجر الجمال. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>
</body>
</html>
