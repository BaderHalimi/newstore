@extends('layouts.app')

@section('title', 'إتمام الطلب')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-8 gradient-text">إتمام الطلب</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('checkout.store') }}" method="POST" class="bg-white rounded-xl shadow-md p-8">
                @csrf

                <!-- Customer Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-6">معلومات الاتصال</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">الاسم الكامل *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            @error('customer_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">البريد الإلكتروني *</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            @error('customer_email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">رقم الهاتف *</label>
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            @error('customer_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-6">عنوان التوصيل</h2>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">العنوان الكامل *</label>
                            <textarea name="shipping_address" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">المدينة *</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                @error('shipping_city')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">المحافظة</label>
                                <input type="text" name="shipping_state" value="{{ old('shipping_state') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">الرمز البريدي</label>
                                <input type="text" name="shipping_zip" value="{{ old('shipping_zip') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">ملاحظات إضافية (اختياري)</label>
                            <textarea name="notes" rows="3"
                                      placeholder="أي ملاحظات خاصة بالطلب أو التوصيل..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-6">طريقة الدفع</h2>
                    <div class="space-y-4">
                        <label class="flex items-center p-4 border-2 border-purple-500 bg-purple-50 rounded-lg cursor-pointer">
                            <input type="radio" name="payment_method" value="cod" checked class="ml-3">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <i class="fas fa-money-bill-wave text-purple-600 text-xl ml-3"></i>
                                    <span class="font-semibold text-gray-900">الدفع عند الاستلام</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1 mr-9">ادفع نقداً عند استلام طلبك</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition opacity-50 cursor-not-allowed">
                            <input type="radio" name="payment_method" value="stripe" disabled class="ml-3">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <i class="fab fa-cc-stripe text-blue-600 text-xl ml-3"></i>
                                    <span class="font-semibold text-gray-900">Stripe</span>
                                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded mr-2">قريباً</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1 mr-9">الدفع ببطاقة الائتمان</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition opacity-50 cursor-not-allowed">
                            <input type="radio" name="payment_method" value="paypal" disabled class="ml-3">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <i class="fab fa-paypal text-blue-500 text-xl ml-3"></i>
                                    <span class="font-semibold text-gray-900">PayPal</span>
                                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded mr-2">قريباً</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1 mr-9">الدفع عبر PayPal</p>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-purple-600 text-white py-4 rounded-lg hover:bg-purple-700 transition text-lg font-semibold">
                    <i class="fas fa-check-circle ml-2"></i>
                    تأكيد الطلب
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                <h2 class="text-2xl font-bold mb-6">ملخص الطلب</h2>

                <!-- Cart Items -->
                <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
                    @foreach($cart->items as $item)
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="flex-shrink-0">
                            @if($item->product->images && count($item->product->images) > 0)
                                <img src="{{ asset('storage/' . $item->product->images[0]) }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-16 gradient-bg rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500">الكمية: {{ $item->quantity }}</p>
                        </div>
                        <p class="text-sm font-bold text-gray-900">
                            {{ number_format($item->getSubtotal(), 0) }} ل.س
                        </p>
                    </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="border-t pt-4 space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>المجموع الفرعي</span>
                        <span class="font-semibold">{{ number_format($cart->getTotal(), 0) }} ل.س</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>الشحن</span>
                        <span class="font-semibold text-green-600">مجاني</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between text-xl font-bold">
                        <span>المجموع الكلي</span>
                        <span class="text-purple-600">{{ number_format($cart->getTotal(), 0) }} ل.س</span>
                    </div>
                </div>

                <!-- Features -->
                <div class="mt-6 pt-6 border-t space-y-3">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-shield-alt text-green-500 ml-2"></i>
                        <span>تسوق آمن ومضمون</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-truck text-green-500 ml-2"></i>
                        <span>شحن سريع لجميع المناطق</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-undo text-green-500 ml-2"></i>
                        <span>إرجاع مجاني خلال 14 يوم</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
