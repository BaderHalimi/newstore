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
                            <p class="text-lg font-semibold text-gray-900">
                                {{ number_format($item->product->getCurrentPrice(), 0) }} {{ $currency_symbol }}
                            </p>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button onclick="updateQuantity({{ $item->id }}, 'decrease')"
                                        class="px-3 py-2 hover:bg-gray-100 transition">
                                    <i class="fas fa-minus text-gray-600"></i>
                                </button>
                                <input type="number"
                                       id="quantity-{{ $item->id }}"
                                       value="{{ $item->quantity }}"
                                       min="1"
                                       max="{{ $item->product->stock }}"
                                       onchange="updateQuantity({{ $item->id }}, 'set', this.value)"
                                       class="w-16 px-2 py-2 text-center border-x border-gray-300 focus:outline-none">
                                <button onclick="updateQuantity({{ $item->id }}, 'increase')"
                                        class="px-3 py-2 hover:bg-gray-100 transition">
                                    <i class="fas fa-plus text-gray-600"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Subtotal & Remove -->
                        <div class="text-left">
                            <p class="text-xl font-bold text-purple-600" id="subtotal-{{ $item->id }}">
                                {{ number_format($item->getSubtotal(), 0) }} {{ $currency_symbol }}
                            </p>
                            <button onclick="removeItem({{ $item->id }})" class="text-red-500 hover:text-red-700 text-sm">
                                <i class="fas fa-trash ml-1"></i>
                                حذف
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Clear Cart -->
            <div class="mt-4">
                <button onclick="clearCart()" class="text-red-500 hover:text-red-700 font-semibold">
                    <i class="fas fa-trash ml-1"></i>
                    تفريغ السلة
                </button>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                <h2 class="text-2xl font-bold mb-6">ملخص الطلب</h2>

                <div class="space-y-4 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>المجموع الفرعي</span>
                        <span class="font-semibold" id="cart-subtotal">{{ number_format($cart->getTotal(), 0) }} {{ $currency_symbol }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>الشحن</span>
                        <span class="font-semibold text-green-600">مجاني</span>
                    </div>
                    <div class="border-t pt-4 flex justify-between text-xl font-bold">
                        <span>المجموع الكلي</span>
                        <span class="text-purple-600" id="cart-total">{{ number_format($cart->getTotal(), 0) }} {{ $currency_symbol }}</span>
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

<!-- Confirmation Modal -->
<div id="confirmModal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2" id="modalTitle">تأكيد الحذف</h3>
                <p class="text-gray-600" id="modalMessage">هل أنت متأكد من هذا الإجراء؟</p>
            </div>
            <div class="flex gap-4">
                <button onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                    إلغاء
                </button>
                <button onclick="confirmAction()" class="flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold">
                    تأكيد
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const currencySymbol = '{{ $currency_symbol }}';
let pendingAction = null;
let pendingItemId = null;

function updateQuantity(itemId, action, value = null) {
    const input = document.getElementById(`quantity-${itemId}`);
    let quantity = parseInt(input.value);
    const max = parseInt(input.max);

    if (action === 'increase') {
        quantity = Math.min(quantity + 1, max);
    } else if (action === 'decrease') {
        quantity = Math.max(quantity - 1, 1);
    } else if (action === 'set') {
        quantity = Math.max(1, Math.min(parseInt(value), max));
    }

    input.value = quantity;

    // إرسال الطلب
    fetch(`/cart/update/${itemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث المجموع الفرعي للمنتج
            document.getElementById(`subtotal-${itemId}`).textContent =
                new Intl.NumberFormat('ar-EG').format(data.itemSubtotal) + ' ' + currencySymbol;

            // تحديث المجموع الكلي
            document.getElementById('cart-subtotal').textContent =
                new Intl.NumberFormat('ar-EG').format(data.cartTotal) + ' ' + currencySymbol;
            document.getElementById('cart-total').textContent =
                new Intl.NumberFormat('ar-EG').format(data.cartTotal) + ' ' + currencySymbol;

            // تحديث عدد المنتجات في الهيدر
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(el => {
                el.textContent = data.cartItemsCount;
            });

            showToast('تم تحديث الكمية بنجاح', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('حدث خطأ في تحديث الكمية', 'error');
    });
}

function showModal(title, message, action, itemId = null) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('confirmModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    pendingAction = action;
    pendingItemId = itemId;
}

function closeModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    document.body.style.overflow = '';
    pendingAction = null;
    pendingItemId = null;
}

function confirmAction() {
    if (pendingAction === 'remove') {
        executeRemoveItem(pendingItemId);
    } else if (pendingAction === 'clear') {
        executeClearCart();
    }
    closeModal();
}

function removeItem(itemId) {
    showModal(
        'حذف المنتج',
        'هل أنت متأكد من حذف هذا المنتج من السلة؟',
        'remove',
        itemId
    );
}

function executeRemoveItem(itemId) {
    fetch(`/cart/remove/${itemId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('تم حذف المنتج من السلة', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('حدث خطأ في حذف المنتج', 'error');
    });
}

function clearCart() {
    showModal(
        'تفريغ السلة',
        'هل أنت متأكد من حذف جميع المنتجات من السلة؟',
        'clear'
    );
}

function executeClearCart() {
    fetch('/cart/clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('تم تفريغ السلة بنجاح', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('حدث خطأ في تفريغ السلة', 'error');
    });
}function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };

    const toast = document.createElement('div');
    toast.className = `fixed top-4 left-1/2 transform -translate-x-1/2 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endsection
