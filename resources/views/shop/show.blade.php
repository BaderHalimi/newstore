@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="flex mb-8 text-sm">
        <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-purple-600">الرئيسية</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('shop.category', $product->category->slug) }}" class="text-gray-500 hover:text-purple-600">
            {{ $product->category->name }}
        </a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-900">{{ $product->name }}</span>
    </nav>

    <!-- Product Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Product Images -->
        <div>
            @if($product->images && count($product->images) > 0)
                <div class="mb-4">
                    <img id="mainImage" src="{{ asset('storage/' . $product->images[0]) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-96 object-cover rounded-xl shadow-lg">
                </div>

                @if(count($product->images) > 1)
                <div class="grid grid-cols-4 gap-4">
                    @foreach($product->images as $index => $image)
                        <img src="{{ asset('storage/' . $image) }}"
                             alt="{{ $product->name }}"
                             onclick="document.getElementById('mainImage').src = this.src"
                             class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-75 transition {{ $index == 0 ? 'ring-2 ring-purple-500' : '' }}">
                    @endforeach
                </div>
                @endif
            @else
                <div class="w-full h-96 gradient-bg rounded-xl flex items-center justify-center">
                    <i class="fas fa-image text-white text-6xl"></i>
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

            <div class="flex items-center mb-6">
                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                    {{ $product->category->name }}
                </span>
                @if($product->is_featured)
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm mr-2">
                        <i class="fas fa-star ml-1"></i>
                        منتج مميز
                    </span>
                @endif
            </div>

            <!-- Price -->
            <div class="mb-6">
                @if($product->isOnSale())
                    <div class="flex items-center gap-4">
                        <span class="text-4xl font-bold text-purple-600">{{ number_format($product->sale_price, 0) }} {{ $currency_symbol }}</span>
                        <span class="text-2xl text-gray-400 line-through mr-4">{{ number_format($product->price, 0) }} {{ $currency_symbol }}</span>
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold mr-4">
                            خصم {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                        </span>
                    </div>
                @else
                    <span class="text-4xl font-bold text-purple-600">{{ number_format($product->price, 0) }} {{ $currency_symbol }}</span>
                @endif
            </div>

            <!-- Stock Status -->
            <div class="mb-6">
                @if($product->stock > 0)
                    <div class="flex items-center text-green-600">
                        <i class="fas fa-check-circle ml-2"></i>
                        <span class="font-semibold">متوفر في المخزون ({{ $product->stock }} قطعة)</span>
                    </div>
                @else
                    <div class="flex items-center text-red-600">
                        <i class="fas fa-times-circle ml-2"></i>
                        <span class="font-semibold">نفذت الكمية</span>
                    </div>
                @endif
            </div>

            <!-- Description -->
            @if($product->description)
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-3">وصف المنتج</h3>
                <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
            </div>
            @endif

            <!-- Add to Cart Form -->
            @if($product->stock > 0)
            <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-8">
                @csrf
                <div class="flex items-center space-x-4 space-x-reverse mb-6">
                    <label class="text-gray-700 font-semibold">الكمية:</label>
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                           class="w-24 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <button type="submit"
                        class="w-full bg-purple-600 text-white py-4 rounded-lg hover:bg-purple-700 transition text-lg font-semibold flex items-center justify-center">
                    <i class="fas fa-shopping-cart ml-2"></i>
                    أضف إلى السلة
                </button>
            </form>
            @endif

            <!-- Product Features -->
            <div class="border-t pt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-purple-600 text-xl ml-3"></i>
                        <span class="text-gray-700">منتج أصلي 100%</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-truck text-purple-600 text-xl ml-3"></i>
                        <span class="text-gray-700">شحن سريع</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-undo text-purple-600 text-xl ml-3"></i>
                        <span class="text-gray-700">إرجاع مجاني</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave text-purple-600 text-xl ml-3"></i>
                        <span class="text-gray-700">دفع عند الاستلام</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section (Google Play Style) -->
    @php
        $approvedReviews = $product->reviews()->where('is_approved', true)->latest()->get();
        $averageRating = $approvedReviews->avg('rating') ?? 0;
        $totalReviews = $approvedReviews->count();
        $ratingCounts = $approvedReviews->groupBy('rating')->map->count();
    @endphp

    @if($totalReviews > 0)
    <div class="mt-20">
        <h2 class="text-3xl font-bold mb-8 gradient-text">التقييمات والمراجعات</h2>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <!-- Rating Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <!-- Average Rating -->
                <div class="flex flex-col items-center justify-center border-l border-gray-200 pl-8">
                    <div class="text-6xlØ¹Ø±Ø¶ Ø¬Ù…ÙŠØ¹ Ø§Ù„ØªÙ‚ÙŠÙŠÙ…Ø§Øª (6) font-bold text-gray-900 mb-2">{{ number_format($averageRating, 1) }}</div>
                    <div class="flex items-center mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }} text-xl"></i>
                        @endfor
                    </div>
                    <div class="text-gray-600 text-sm">{{ $totalReviews }} تقييم</div>
                </div>

                <!-- Rating Breakdown -->
                <div class="md:col-span-2 space-y-3">
                    @for($rating = 5; $rating >= 1; $rating--)
                        @php
                            $count = $ratingCounts[$rating] ?? 0;
                            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1 w-16">
                                <span class="text-sm font-medium text-gray-700">{{ $rating }}</span>
                                <i class="fas fa-star text-yellow-400 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-yellow-400 rounded-full transition-all duration-500"
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <div class="w-16 text-sm text-gray-600 text-left">{{ $count }}</div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Reviews List -->
            <div class="space-y-6">
                @foreach($approvedReviews->take(5) as $review)
                <div class="border-b border-gray-200 pb-6 last:border-0">
                    <div class="flex items-start gap-4">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            @if($review->customer && $review->customer->avatar)
                                <img src="{{ asset('storage/' . $review->customer->avatar) }}"
                                     alt="{{ $review->customer_name }}"
                                     class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg">
                                    {{ mb_substr($review->customer_name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Review Content -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">{{ $review->customer_name }}</h4>
                                <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                            </div>

                            <!-- Star Rating -->
                            <div class="flex items-center mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                @endfor
                            </div>

                            <!-- Comment -->
                            @if($review->comment)
                            <p class="text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-20">
        <h2 class="text-3xl font-bold mb-8 gradient-text">منتجات ذات صلة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition overflow-hidden hover-scale">
                @if($relatedProduct->images && count($relatedProduct->images) > 0)
                    <img src="{{ asset('storage/' . $relatedProduct->images[0]) }}"
                         alt="{{ $relatedProduct->name }}"
                         class="w-full h-64 object-cover">
                @else
                    <div class="w-full h-64 gradient-bg flex items-center justify-center">
                        <i class="fas fa-image text-white text-4xl"></i>
                    </div>
                @endif

                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>

                    <div class="flex items-center justify-between mb-4">
                        @if($relatedProduct->isOnSale())
                            <div>
                                <span class="text-xl font-bold text-purple-600">{{ number_format($relatedProduct->sale_price, 0) }} {{ $currency_symbol }}</span>
                                <span class="text-sm text-gray-400 line-through mr-2">{{ number_format($relatedProduct->price, 0) }} {{ $currency_symbol }}</span>
                            </div>
                        @else
                            <span class="text-xl font-bold text-purple-600">{{ number_format($relatedProduct->price, 0) }} {{ $currency_symbol }}</span>
                        @endif
                    </div>

                    <a href="{{ route('shop.show', $relatedProduct->slug) }}"
                       class="block w-full bg-purple-600 text-white text-center py-2 rounded-lg hover:bg-purple-700 transition">
                        عرض التفاصيل
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
