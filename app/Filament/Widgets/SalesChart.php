<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'المبيعات الشهرية';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = $this->getSalesPerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'المبيعات',
                    'data' => $data['sales'],
                    'borderColor' => '#667eea',
                    'backgroundColor' => 'rgba(102, 126, 234, 0.1)',
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getSalesPerMonth(): array
    {
        $now = Carbon::now();
        $months = [];
        $sales = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $months[] = $month->format('M Y');

            $monthlySales = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total');

            $sales[] = (float) $monthlySales;
        }

        return [
            'months' => $months,
            'sales' => $sales,
        ];
    }
}
