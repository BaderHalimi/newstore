@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Category Header -->
    <div class="text-center mb-12">
        @if($category->image)
            <img src="{{ asset('storage/' . $category->image) }}"
                 alt="{{ $category->name }}"
                 class="w-32 h-32 mx-auto mb-4 rounded-full object-cover shadow-lg">
        @else
            <div class="w-32 h-32 mx-auto mb-4 gradient-bg rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-tag text-white text-5xl"></i>
            </div>
        @endif

        <h1 class="text-4xl font-bold gradient-text mb-4">{{ $category->name }}</h1>

        @if($category->description)
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ $category->description }}</p>
        @endif

        <p class="text-gray-500 mt-2">{{ $products->total() }} منتج</p>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex mb-8 text-sm">
        <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-purple-600">الرئيسية</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-900">{{ $category->name }}</span>
    </nav>

    <!-- Products Grid -->
    @if($products->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
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

                <div class="flex items-center justify-between mb-4">
                    @if($product->isOnSale())
                        <div>
                            <span class="text-2xl font-bold text-purple-600">{{ number_format($product->sale_price, 0) }} {{ $currency_symbol }}</span>
                            <span class="text-sm text-gray-400 line-through mr-2">{{ number_format($product->price, 0) }} {{ $currency_symbol }}</span>
                        </div>
                    @else
                        <span class="text-2xl font-bold text-purple-600">{{ number_format($product->price, 0) }} {{ $currency_symbol }}</span>
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
    <!-- No Products -->
    <div class="text-center py-12">
        <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
        <p class="text-xl text-gray-500">لا توجد منتجات في هذه الفئة حالياً</p>
        <a href="{{ route('shop.index') }}" class="inline-block mt-6 bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
            العودة إلى المتجر
        </a>
    </div>
    @endif
</div>
@endsection
