<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'الأرباح مقابل المصروفات';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = $this->getRevenueData();

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات',
                    'data' => $data['revenue'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                ],
                [
                    'label' => 'التكاليف',
                    'data' => $data['costs'],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                ],
                [
                    'label' => 'صافي الربح',
                    'data' => $data['profit'],
                    'backgroundColor' => 'rgba(102, 126, 234, 0.5)',
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getRevenueData(): array
    {
        $now = Carbon::now();
        $months = [];
        $revenue = [];
        $costs = [];
        $profit = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $months[] = $month->format('M');

            $monthlyRevenue = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total');

            // تكلفة تقديرية 40% من الإيرادات
            $monthlyCost = $monthlyRevenue * 0.4;

            $revenue[] = (float) $monthlyRevenue;
            $costs[] = (float) $monthlyCost;
            $profit[] = (float) ($monthlyRevenue - $monthlyCost);
        }

        return [
            'months' => $months,
            'revenue' => $revenue,
            'costs' => $costs,
            'profit' => $profit,
        ];
    }
}
