<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $currencySymbol = Setting::get('currency_symbol', '₪');
        
        return [
            Stat::make('إجمالي المبيعات', $currencySymbol . ' ' . number_format($this->getTotalSales(), 0))
                ->description('إجمالي المبيعات الكلية')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($this->getSalesChart()),

            Stat::make('مبيعات هذا الشهر', $currencySymbol . ' ' . number_format($this->getMonthSales(), 0))
                ->description($this->getMonthComparison())
                ->descriptionIcon($this->getMonthTrend() > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($this->getMonthTrend() > 0 ? 'success' : 'danger'),

            Stat::make('الطلبات الجديدة', $this->getPendingOrders())
                ->description('بانتظار المعالجة')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make('صافي الأرباح', $currencySymbol . ' ' . number_format($this->getProfit(), 0))
                ->description('الأرباح الصافية')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('عدد المنتجات', $this->getProductsCount())
                ->description($this->getLowStockCount() . ' منتج بمخزون منخفض')
                ->descriptionIcon('heroicon-m-cube')
                ->color($this->getLowStockCount() > 0 ? 'warning' : 'success'),

            Stat::make('متوسط قيمة الطلب', $currencySymbol . ' ' . number_format($this->getAverageOrderValue(), 0))
                ->description('متوسط سعر الطلب الواحد')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
        ];
    }

    private function getTotalSales(): float
    {
        return Order::whereNotIn('status', ['cancelled'])->sum('total');
    }

    private function getMonthSales(): float
    {
        return Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');
    }

    private function getMonthComparison(): string
    {
        $currentMonth = $this->getMonthSales();
        $lastMonth = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        if ($lastMonth == 0) {
            return 'لا توجد بيانات للمقارنة';
        }

        $percentage = (($currentMonth - $lastMonth) / $lastMonth) * 100;

        if ($percentage > 0) {
            return '+' . number_format($percentage, 1) . '% من الشهر الماضي';
        } else {
            return number_format($percentage, 1) . '% من الشهر الماضي';
        }
    }

    private function getMonthTrend(): float
    {
        $currentMonth = $this->getMonthSales();
        $lastMonth = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        return $currentMonth - $lastMonth;
    }

    private function getPendingOrders(): int
    {
        return Order::where('status', 'pending')->count();
    }

    private function getProfit(): float
    {
        $revenue = $this->getTotalSales();
        // تكلفة تقديرية 40% من الإيرادات
        $costs = $revenue * 0.4;
        return $revenue - $costs;
    }

    private function getProductsCount(): int
    {
        return Product::count();
    }

    private function getLowStockCount(): int
    {
        return Product::where('stock', '<', 10)->where('is_active', true)->count();
    }

    private function getAverageOrderValue(): float
    {
        $total = Order::whereNotIn('status', ['cancelled'])->sum('total');
        $count = Order::whereNotIn('status', ['cancelled'])->count();

        return $count > 0 ? $total / $count : 0;
    }

    private function getSalesChart(): array
    {
        $sales = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailySales = Order::whereDate('created_at', $date)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total');
            $sales[] = (float) $dailySales;
        }

        return $sales;
    }
}
