@extends('layouts.app')

@section('title', 'متجر مستحضرات التجميل')

@section('content')
<!-- Hero Section -->
<div class="gradient-bg text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 animate-fade-in">
                اكتشفي جمالك الطبيعي
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-purple-100">
                أفضل مستحضرات التجميل الأصلية بأسعار منافسة
            </p>
            <a href="#products" class="bg-white text-purple-600 px-8 py-4 rounded-full text-lg font-semibold hover:bg-gray-100 transition inline-block">
                تسوقي الآن
                <i class="fas fa-arrow-down mr-2"></i>
            </a>
        </div>
    </div>
</div>

<!-- Categories Section -->
@if($categories->count() > 0)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-center mb-12 gradient-text">تسوقي حسب الفئة</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($categories as $category)
        <a href="{{ route('shop.category', $category->slug) }}"
           class="bg-white rounded-xl shadow-md hover:shadow-xl transition p-6 text-center hover-scale">
            @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}"
                     alt="{{ $category->name }}"
                     class="w-20 h-20 mx-auto mb-4 rounded-full object-cover">
            @else
                <div class="w-20 h-20 mx-auto mb-4 gradient-bg rounded-full flex items-center justify-center">
                    <i class="fas fa-tag text-white text-2xl"></i>
                </div>
            @endif
            <h3 class="text-lg font-semibold text-gray-800">{{ $category->name }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $category->products_count }} منتج</p>
        </a>
        @endforeach
    </div>
</div>
@endif

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<div class="bg-purple-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12 gradient-text">منتجات مميزة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition overflow-hidden hover-scale">
                @if($product->images && count($product->images) > 0)
                    <img src="{{ asset('storage/' . $product->images[0]) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-64 object-cover">
                @else
                    <div class="w-full h-64 gradient-bg flex items-center justify-center">
                        <i class="fas fa-image text-white text-4xl"></i>
                    </div>
                @endif

                @if($product->isOnSale())
                    <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                        خصم!
                    </div>
                @endif

                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $product->category->name }}</p>

                    <div class="flex items-center justify-between mb-4">
                        @if($product->isOnSale())
                            <div>
                                <span class="text-2xl font-bold text-purple-600">{{ number_format($product->sale_price, 0) }} ل.س</span>
                                <span class="text-sm text-gray-400 line-through mr-2">{{ number_format($product->price, 0) }} ل.س</span>
                            </div>
                        @else
                            <span class="text-2xl font-bold text-purple-600">{{ number_format($product->price, 0) }} ل.س</span>
                        @endif
                    </div>

                    <a href="{{ route('shop.show', $product->slug) }}"
                       class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                        عرض التفاصيل
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- All Products -->
<div id="products" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-center mb-12 gradient-text">جميع المنتجات</h2>

    @if($products->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition overflow-hidden hover-scale">
            <div class="relative">
                @if($product->images && count($product->images) > 0)
                    <img src="{{ asset('storage/' . $product->images[0]) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-64 object-cover">
                @else
                    <div class="w-full h-64 gradient-bg flex items-center justify-center">
                        <i class="fas fa-image text-white text-4xl"></i>
                    </div>
                @endif

                @if($product->isOnSale())
                    <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                        خصم!
                    </div>
                @endif

                @if($product->stock == 0)
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                        <span class="text-white text-xl font-bold">نفذت الكمية</span>
                    </div>
                @endif
            </div>

            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ $product->category->name }}</p>

                <div class="flex items-center justify-between mb-4">
                    @if($product->isOnSale())
                        <div>
                            <span class="text-2xl font-bold text-purple-600">{{ number_format($product->sale_price, 0) }} ل.س</span>
                            <span class="text-sm text-gray-400 line-through mr-2">{{ number_format($product->price, 0) }} ل.س</span>
                        </div>
                    @else
                        <span class="text-2xl font-bold text-purple-600">{{ number_format($product->price, 0) }} ل.س</span>
                    @endif
                </div>

                <a href="{{ route('shop.show', $product->slug) }}"
                   class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                    عرض التفاصيل
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $products->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
        <p class="text-xl text-gray-500">لا توجد منتجات متاحة حالياً</p>
    </div>
    @endif
</div>

<!-- Features Section -->
<div class="bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shipping-fast text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">شحن سريع</h3>
                <p class="text-gray-600">توصيل سريع لجميع المناطق</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">منتجات أصلية</h3>
                <p class="text-gray-600">ضمان أصالة المنتجات 100%</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">دعم 24/7</h3>
                <p class="text-gray-600">خدمة عملاء متاحة طوال الوقت</p>
            </div>
        </div>
    </div>
</div>
@endsection
