@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="flex mb-8 text-sm">
        <a href="{{ route('account.orders') }}" class="text-gray-500 hover:text-purple-600">
            <i class="fas fa-arrow-right ml-1"></i>
            العودة إلى طلباتي
        </a>
    </nav>

    <!-- Order Header -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    طلب رقم: {{ $order->order_number }}
                </h1>
                <p class="text-gray-600">
                    <i class="fas fa-calendar ml-1"></i>
                    تاريخ الطلب: {{ $order->created_at->format('Y-m-d H:i') }}
                </p>
            </div>

            <div class="mt-4 md:mt-0">
                <span class="px-6 py-3 rounded-full text-lg font-semibold inline-block
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
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold mb-6">المنتجات</h2>
        <div class="space-y-4">
            @foreach($order->items as $item)
            <div class="flex items-center justify-between border-b pb-4 last:border-b-0">
                <div class="flex items-center space-x-4 space-x-reverse">
                    @if($item->product && $item->product->images && count($item->product->images) > 0)
                        <img src="{{ asset('storage/' . $item->product->images[0]) }}"
                             alt="{{ $item->product_name }}"
                             class="w-20 h-20 object-cover rounded-lg">
                    @else
                        <div class="w-20 h-20 gradient-bg rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-white text-xl"></i>
                        </div>
                    @endif

                    <div>
                        <h3 class="font-semibold text-gray-900 text-lg">{{ $item->product_name }}</h3>
                        <p class="text-gray-600">{{ number_format($item->price, 0) }} {{ $currency_symbol }} × {{ $item->quantity }}</p>
                    </div>
                </div>

                <p class="text-xl font-bold text-gray-900">
                    {{ number_format($item->total, 0) }} {{ $currency_symbol }}
                </p>
            </div>
            @endforeach
        </div>

        <!-- Order Totals -->
        <div class="border-t mt-6 pt-6 space-y-3">
            <div class="flex justify-between text-gray-600">
                <span>المجموع الفرعي</span>
                <span class="font-semibold">{{ number_format($order->subtotal, 0) }} {{ $currency_symbol }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>الشحن</span>
                <span class="font-semibold">
                    @if($order->shipping_cost > 0)
                        {{ number_format($order->shipping_cost, 0) }} {{ $currency_symbol }}
                    @else
                        <span class="text-green-600">مجاني</span>
                    @endif
                </span>
            </div>
            @if($order->tax > 0)
            <div class="flex justify-between text-gray-600">
                <span>الضريبة</span>
                <span class="font-semibold">{{ number_format($order->tax, 0) }} {{ $currency_symbol }}</span>
            </div>
            @endif
            @if($order->discount > 0)
            <div class="flex justify-between text-green-600">
                <span>الخصم</span>
                <span class="font-semibold">-{{ number_format($order->discount, 0) }} {{ $currency_symbol }}</span>
            </div>
            @endif
            <div class="border-t pt-3 flex justify-between text-2xl font-bold">
                <span>المجموع الكلي</span>
                <span class="text-purple-600">{{ number_format($order->total, 0) }} {{ $currency_symbol }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Shipping Information -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-map-marker-alt text-purple-600 ml-2"></i>
                عنوان التوصيل
            </h2>
            <div class="space-y-2 text-gray-700">
                <p><span class="font-semibold">الاسم:</span> {{ $order->customer_name }}</p>
                <p><span class="font-semibold">الهاتف:</span> {{ $order->customer_phone }}</p>
                <p><span class="font-semibold">العنوان:</span> {{ $order->shipping_address }}</p>
                <p><span class="font-semibold">المدينة:</span> {{ $order->shipping_city }}</p>
                @if($order->shipping_state)
                <p><span class="font-semibold">المحافظة:</span> {{ $order->shipping_state }}</p>
                @endif
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-credit-card text-purple-600 ml-2"></i>
                معلومات الدفع
            </h2>
            <div class="space-y-2 text-gray-700">
                <p>
                    <span class="font-semibold">طريقة الدفع:</span>
                    @if($order->payment_method === 'cod')
                        <span class="text-green-600">الدفع عند الاستلام</span>
                    @elseif($order->payment_method === 'stripe')
                        Stripe
                    @elseif($order->payment_method === 'paypal')
                        PayPal
                    @endif
                </p>
                <p>
                    <span class="font-semibold">حالة الدفع:</span>
                    <span class="
                        @if($order->payment_status === 'paid')
                            text-green-600
                        @elseif($order->payment_status === 'pending')
                            text-yellow-600
                        @else
                            text-red-600
                        @endif
                    ">
                        @switch($order->payment_status)
                            @case('paid')
                                مدفوع
                                @break
                            @case('pending')
                                قيد الانتظار
                                @break
                            @case('failed')
                                فشل
                                @break
                            @default
                                {{ $order->payment_status }}
                        @endswitch
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($order->notes)
    <div class="bg-white rounded-xl shadow-md p-6 mt-6">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <i class="fas fa-sticky-note text-purple-600 ml-2"></i>
            ملاحظات
        </h2>
        <p class="text-gray-700">{{ $order->notes }}</p>
    </div>
    @endif

    <!-- Contact Support -->
    <div class="bg-purple-50 rounded-xl p-6 mt-6 text-center">
        <h3 class="text-lg font-bold text-gray-900 mb-2">هل تحتاج مساعدة؟</h3>
        <p class="text-gray-600 mb-4">تواصل معنا لأي استفسار حول طلبك</p>
        <div class="flex justify-center gap-4">
            <a href="#" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition">
                <i class="fab fa-whatsapp ml-2"></i>
                واتساب
            </a>
            <a href="mailto:support@example.com" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-envelope ml-2"></i>
                البريد
            </a>
        </div>
    </div>
</div>
@endsection
