@extends('layouts.app')

@section('title', 'تم تأكيد الطلب بنجاح')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-xl shadow-lg p-8 md:p-12 text-center">
        <!-- Success Icon -->
        <div class="mb-8">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-circle text-green-500 text-5xl"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">تم تأكيد طلبك بنجاح!</h1>
            <p class="text-lg text-gray-600 mb-2">شكراً لك على طلبك من متجر الجمال</p>
            <p class="text-gray-500">سيتم التواصل معك قريباً لتأكيد الطلب وترتيب التوصيل</p>
        </div>

        <!-- Order Details -->
        <div class="bg-purple-50 rounded-lg p-6 mb-8 text-right">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">رقم الطلب</p>
                    <p class="text-xl font-bold text-purple-600">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">حالة الطلب</p>
                    <p class="text-lg font-semibold text-gray-900">
                        @switch($order->status)
                            @case('pending')
                                <span class="text-yellow-600">قيد الانتظار</span>
                                @break
                            @case('processing')
                                <span class="text-blue-600">قيد المعالجة</span>
                                @break
                            @case('shipped')
                                <span class="text-purple-600">تم الشحن</span>
                                @break
                            @case('delivered')
                                <span class="text-green-600">تم التوصيل</span>
                                @break
                            @case('cancelled')
                                <span class="text-red-600">ملغي</span>
                                @break
                            @default
                                {{ $order->status }}
                        @endswitch
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">طريقة الدفع</p>
                    <p class="text-lg font-semibold text-gray-900">
                        @if($order->payment_method === 'cod')
                            <i class="fas fa-money-bill-wave ml-1"></i>
                            الدفع عند الاستلام
                        @elseif($order->payment_method === 'stripe')
                            <i class="fab fa-cc-stripe ml-1"></i>
                            Stripe
                        @elseif($order->payment_method === 'paypal')
                            <i class="fab fa-paypal ml-1"></i>
                            PayPal
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">المبلغ الإجمالي</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($order->total, 0) }} {{ $currency_symbol }}</p>
                </div>
            </div>
        </div>

        <!-- Delivery Information -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8 text-right">
            <h3 class="text-lg font-bold text-gray-900 mb-4">معلومات التوصيل</h3>
            <div class="space-y-2 text-gray-700">
                <p><span class="font-semibold">الاسم:</span> {{ $order->customer_name }}</p>
                <p><span class="font-semibold">الهاتف:</span> {{ $order->customer_phone }}</p>
                <p><span class="font-semibold">البريد الإلكتروني:</span> {{ $order->customer_email }}</p>
                <p><span class="font-semibold">العنوان:</span> {{ $order->shipping_address }}، {{ $order->shipping_city }}</p>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8 text-right">
            <h3 class="text-lg font-bold text-gray-900 mb-4">تفاصيل الطلب</h3>
            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between border-b pb-3 last:border-b-0">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        @if($item->product && $item->product->images && count($item->product->images) > 0)
                            <img src="{{ asset('storage/' . $item->product->images[0]) }}"
                                 alt="{{ $item->product_name }}"
                                 class="w-16 h-16 object-cover rounded-lg">
                        @else
                            <div class="w-16 h-16 gradient-bg rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-white"></i>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                            <p class="text-sm text-gray-600">الكمية: {{ $item->quantity }}</p>
                        </div>
                    </div>
                    <p class="font-bold text-gray-900">{{ number_format($item->total, 0) }} {{ $currency_symbol }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('shop.index') }}"
               class="bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                <i class="fas fa-shopping-bag ml-2"></i>
                متابعة التسوق
            </a>

            @auth
            <a href="{{ route('account.orders') }}"
               class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                <i class="fas fa-box ml-2"></i>
                عرض طلباتي
            </a>
            @endauth
        </div>

        <!-- Contact Info -->
        <div class="mt-8 pt-8 border-t">
            <p class="text-gray-600">
                لأي استفسار، يمكنك التواصل معنا عبر:
            </p>
            <div class="flex justify-center gap-4 mt-4">
                <a href="#" class="text-purple-600 hover:text-purple-700">
                    <i class="fab fa-whatsapp text-2xl"></i>
                </a>
                <a href="mailto:support@example.com" class="text-purple-600 hover:text-purple-700">
                    <i class="fas fa-envelope text-2xl"></i>
                </a>
                <a href="tel:+963123456789" class="text-purple-600 hover:text-purple-700">
                    <i class="fas fa-phone text-2xl"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
