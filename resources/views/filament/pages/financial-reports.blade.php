<x-filament-panels::page>
    <div class="space-y-6">
        {{-- نموذج اختيار الفترة --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form wire:submit="updateReport">
                {{ $this->form }}

                <div class="mt-4">
                    <x-filament::button type="submit" color="primary">
                        تحديث التقرير
                    </x-filament::button>
                </div>
            </form>
        </div>

        @php
            $stats = $this->getStats();
        @endphp

        {{-- بطاقات الإحصائيات --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- إجمالي المبيعات --}}
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">إجمالي المبيعات</p>
                        <h3 class="text-3xl font-bold mt-2">{{ number_format($stats['total_sales'], 0) }}</h3>
                        <p class="text-xs mt-1 opacity-75">ليرة سورية</p>
                    </div>
                    <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            {{-- عدد الطلبات --}}
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">عدد الطلبات</p>
                        <h3 class="text-3xl font-bold mt-2">{{ number_format($stats['orders_count'], 0) }}</h3>
                        <p class="text-xs mt-1 opacity-75">طلب</p>
                    </div>
                    <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>

            {{-- صافي الربح --}}
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">صافي الربح</p>
                        <h3 class="text-3xl font-bold mt-2">{{ number_format($stats['net_profit'], 0) }}</h3>
                        <p class="text-xs mt-1 opacity-75">ليرة سورية</p>
                    </div>
                    <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>

            {{-- متوسط قيمة الطلب --}}
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">متوسط قيمة الطلب</p>
                        <h3 class="text-3xl font-bold mt-2">{{ number_format($stats['average_order_value'], 0) }}</h3>
                        <p class="text-xs mt-1 opacity-75">ليرة سورية</p>
                    </div>
                    <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- الإيرادات والمصروفات --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- الإيرادات --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-green-600 dark:text-green-400 mb-4">الإيرادات</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ number_format($stats['income'], 0) }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">ليرة سورية</p>
            </div>

            {{-- المصروفات --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">المصروفات</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ number_format($stats['expenses'], 0) }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">ليرة سورية</p>
            </div>

            {{-- الاسترجاعات --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-yellow-600 dark:text-yellow-400 mb-4">الاسترجاعات</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ number_format($stats['refunds'], 0) }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">ليرة سورية</p>
            </div>
        </div>

        @php
            $bestSelling = $this->getBestSellingProducts();
        @endphp

        {{-- أفضل المنتجات مبيعاً --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">أفضل 10 منتجات مبيعاً</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">المنتج</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">الكمية المباعة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">إجمالي الإيرادات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($bestSelling as $index => $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $product['name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
                                        {{ number_format($product['total_sold'], 0) }} قطعة
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">
                                    {{ number_format($product['total_revenue'], 0) }} ل.س
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    لا توجد مبيعات في هذه الفترة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            $expensesByCategory = $this->getExpensesByCategory();
        @endphp

        {{-- المصروفات حسب التصنيف --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">المصروفات حسب التصنيف</h3>
            </div>
            <div class="p-6">
                @forelse($expensesByCategory as $expense)
                    <div class="mb-4 last:mb-0">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $expense['category'] }}</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($expense['total'], 0) }} ل.س</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            @php
                                $percentage = $stats['expenses'] > 0 ? ($expense['total'] / $stats['expenses']) * 100 : 0;
                            @endphp
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($percentage, 1) }}%</span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 dark:text-gray-400">لا توجد مصروفات في هذه الفترة</p>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
