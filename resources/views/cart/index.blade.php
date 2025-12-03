@extends('layouts.app')

@section('title', 'سلة التسوق')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-8 gradient-text">سلة التسوق</h1>

    @if($cart && $cart->items->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                @foreach($cart->items as $item)
                <div class="p-6 border-b last:border-b-0 hover:bg-gray-50 transition">
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <!-- Product Image -->
                        <div class="flex-shrink-0">
                            @if($item->product->images && count($item->product->images) > 0)
                                <img src="{{ asset('storage/' . $item->product->images[0]) }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-24 h-24 object-cover rounded-lg">
                            @else
                                <div class="w-24 h-24 gradient-bg rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-white text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Product Details -->
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                <a href="{{ route('shop.show', $item->product->slug) }}" class="hover:text-purple-600">
                                    {{ $item->product->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 mb-2">{{ $item->product->category->name }}</p>
                            <p class="text-lg font-bold text-purple-600">
                                {{ number_format($item->product->getCurrentPrice(), 0) }} ل.س
                            </p>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item->quantity }}"
                                       min="1" max="{{ $item->product->stock }}"
                                       onchange="this.form.submit()"
                                       class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-center">
                            </form>
                        </div>

                        <!-- Subtotal & Remove -->
                        <div class="text-left">
                            <p class="text-xl font-bold text-gray-900 mb-2">
                                {{ number_format($item->getSubtotal(), 0) }} ل.س
                            </p>
                            <form action="{{ route('cart.remove', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-trash ml-1"></i>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Clear Cart -->
            <form action="{{ route('cart.clear') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">
                    <i class="fas fa-trash ml-1"></i>
                    تفريغ السلة
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                <h2 class="text-2xl font-bold mb-6">ملخص الطلب</h2>

                <div class="space-y-4 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>المجموع الفرعي</span>
                        <span class="font-semibold">{{ number_format($cart->getTotal(), 0) }} ل.س</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>الشحن</span>
                        <span class="font-semibold text-green-600">مجاني</span>
                    </div>
                    <div class="border-t pt-4 flex justify-between text-xl font-bold">
                        <span>المجموع الكلي</span>
                        <span class="text-purple-600">{{ number_format($cart->getTotal(), 0) }} ل.س</span>
                    </div>
                </div>

                <a href="{{ route('checkout.index') }}"
                   class="block w-full bg-purple-600 text-white text-center py-4 rounded-lg hover:bg-purple-700 transition text-lg font-semibold">
                    متابعة إلى الدفع
                    <i class="fas fa-arrow-left mr-2"></i>
                </a>

                <a href="{{ route('shop.index') }}"
                   class="block w-full text-center mt-4 text-purple-600 hover:text-purple-700 font-semibold">
                    <i class="fas fa-arrow-right ml-2"></i>
                    متابعة التسوق
                </a>
            </div>
        </div>
    </div>

    @else
    <!-- Empty Cart -->
    <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-6"></i>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">سلة التسوق فارغة</h2>
        <p class="text-gray-600 mb-8">لم تقم بإضافة أي منتجات إلى السلة بعد</p>
        <a href="{{ route('shop.index') }}"
           class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
            <i class="fas fa-shopping-bag ml-2"></i>
            تصفح المنتجات
        </a>
    </div>
    @endif
</div>
@endsection
