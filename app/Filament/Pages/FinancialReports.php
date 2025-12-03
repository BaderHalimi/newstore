<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class FinancialReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'التقارير المالية';

    protected static ?string $title = 'التقارير المالية';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'الإدارة المالية';

    protected static string $view = 'filament.pages.financial-reports';

    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        $this->form->fill([
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('فترة التقرير')
                    ->schema([
                        DatePicker::make('date_from')
                            ->label('من تاريخ')
                            ->native(false)
                            ->default(now()->startOfMonth()),
                        DatePicker::make('date_to')
                            ->label('إلى تاريخ')
                            ->native(false)
                            ->default(now()->endOfMonth()),
                    ])
                    ->columns(2),
            ]);
    }

    public function getStats(): array
    {
        $from = Carbon::parse($this->dateFrom ?? now()->startOfMonth());
        $to = Carbon::parse($this->dateTo ?? now()->endOfMonth());

        // إجمالي المبيعات
        $totalSales = Order::whereBetween('created_at', [$from, $to])
            ->whereIn('status', ['completed', 'processing', 'shipped'])
            ->sum('total');

        // عدد الطلبات
        $ordersCount = Order::whereBetween('created_at', [$from, $to])->count();

        // الإيرادات
        $income = Transaction::where('type', 'income')
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        // المصروفات
        $expenses = Transaction::where('type', 'expense')
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        // الاسترجاعات
        $refunds = Transaction::where('type', 'refund')
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        // صافي الربح (تقريبي: 60% من المبيعات - المصروفات - الاسترجاعات)
        $netProfit = ($totalSales * 0.6) - $expenses - $refunds;

        // متوسط قيمة الطلب
        $averageOrderValue = $ordersCount > 0 ? $totalSales / $ordersCount : 0;

        return [
            'total_sales' => $totalSales,
            'orders_count' => $ordersCount,
            'income' => $income,
            'expenses' => $expenses,
            'refunds' => $refunds,
            'net_profit' => $netProfit,
            'average_order_value' => $averageOrderValue,
        ];
    }

    public function getBestSellingProducts(): array
    {
        $from = Carbon::parse($this->dateFrom ?? now()->startOfMonth());
        $to = Carbon::parse($this->dateTo ?? now()->endOfMonth());

        return Product::query()
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereIn('orders.status', ['completed', 'processing', 'shipped'])
            ->selectRaw('products.*, SUM(order_items.quantity) as total_sold, SUM(order_items.quantity * order_items.price) as total_revenue')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function getExpensesByCategory(): array
    {
        $from = Carbon::parse($this->dateFrom ?? now()->startOfMonth());
        $to = Carbon::parse($this->dateTo ?? now()->endOfMonth());

        return Transaction::where('type', 'expense')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->map(function ($item) {
                $categories = [
                    'shipping' => 'شحن',
                    'marketing' => 'تسويق',
                    'supplies' => 'لوازم',
                    'salaries' => 'رواتب',
                    'rent' => 'إيجار',
                    'utilities' => 'خدمات',
                    'other_expense' => 'مصروفات أخرى',
                ];

                return [
                    'category' => $categories[$item->category] ?? $item->category,
                    'total' => $item->total,
                ];
            })
            ->toArray();
    }

    public function updateReport(): void
    {
        $data = $this->form->getState();
        $this->dateFrom = $data['date_from'];
        $this->dateTo = $data['date_to'];
    }
}
