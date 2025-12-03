@extends('layouts.app')

@section('title', 'طلباتي')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-8 gradient-text">طلباتي</h1>

    @if($orders->count() > 0)
    <div class="space-y-6">
        @foreach($orders as $order)
        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            طلب رقم: {{ $order->order_number }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-calendar ml-1"></i>
                            {{ $order->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>

                    <div class="mt-4 md:mt-0 flex flex-col items-start md:items-end space-y-2">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold
                            @switch($order->status)
                                @case('pending')
                                    bg-yellow-100 text-yellow-800
                                    @break
                                @case('processing')
                                    bg-blue-100 text-blue-800
                                    @break
                                @case('shipped')
                                    bg-purple-100 text-purple-800
                                    @break
                                @case('delivered')
                                    bg-green-100 text-green-800
                                    @break
                                @case('cancelled')
                                    bg-red-100 text-red-800
                                    @break
                            @endswitch">
                            @switch($order->status)
                                @case('pending')
                                    قيد الانتظار
                                    @break
                                @case('processing')
                                    قيد المعالجة
                                    @break
                                @case('shipped')
                                    تم الشحن
                                    @break
                                @case('delivered')
                                    تم التوصيل
                                    @break
                                @case('cancelled')
                                    ملغي
                                    @break
                            @endswitch
                        </span>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ number_format($order->total, 0) }} {{ $currency_symbol }}
                        </p>
                    </div>
                </div>

                <!-- Order Items Preview -->
                <div class="border-t pt-4 mb-4">
                    <div class="flex items-center space-x-4 space-x-reverse overflow-x-auto pb-2">
                        @foreach($order->items->take(3) as $item)
                        <div class="flex-shrink-0">
                            @if($item->product && $item->product->images && count($item->product->images) > 0)
                                <img src="{{ asset('storage/' . $item->product->images[0]) }}"
                                     alt="{{ $item->product_name }}"
                                     class="w-20 h-20 object-cover rounded-lg">
                            @else
                                <div class="w-20 h-20 gradient-bg rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-white text-xl"></i>
                                </div>
                            @endif
                        </div>
                        @endforeach

                        @if($order->items->count() > 3)
                        <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                            <span class="text-gray-600 font-bold">+{{ $order->items->count() - 3 }}</span>
                        </div>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        {{ $order->items->count() }} منتج
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('account.order-details', $order) }}"
                       class="flex-1 bg-purple-600 text-white text-center py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                        <i class="fas fa-eye ml-1"></i>
                        عرض التفاصيل
                    </a>

                    @if($order->payment_method === 'cod')
                    <span class="flex-1 bg-gray-100 text-gray-700 text-center py-3 rounded-lg font-semibold">
                        <i class="fas fa-money-bill-wave ml-1"></i>
                        الدفع عند الاستلام
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @else
    <!-- No Orders -->
    <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <i class="fas fa-box-open text-6xl text-gray-300 mb-6"></i>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">لا توجد طلبات بعد</h2>
        <p class="text-gray-600 mb-8">لم تقم بإنشاء أي طلبات حتى الآن</p>
        <a href="{{ route('shop.index') }}"
           class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
            <i class="fas fa-shopping-bag ml-2"></i>
            ابدأ التسوق الآن
        </a>
    </div>
    @endif
</div>
@endsection
