<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\CreditUsed;
use Carbon\Carbon;

class ServicesSpentRecently extends ChartWidget
{
    protected static ?string $heading = 'Services Used';
    protected static ?int $sort = 2;

    protected function getFilters(): ?array
    {
        return [
            'this_month' => 'This Month',
            'this_week' => 'This Week',
            'last_week' => 'Last Week',
            'last_month' => 'Last Month',
            'this_year' => 'This Year',
            'last_year' => 'Last Year',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? 'this_month';
        $now = Carbon::now();

        // Define date ranges based on the selected filter
        switch ($filter) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'last_week':
                $start = $now->copy()->subWeek()->startOfWeek();
                $end = $now->copy()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'last_year':
                $start = $now->copy()->subYear()->startOfYear();
                $end = $now->copy()->subYear()->endOfYear();
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
        }

        // Fetch data grouped by weeks or days based on the filter
        $servicesUsed = CreditUsed::whereBetween('date_used', [$start, $end])
            ->selectRaw("DATE(date_used) as date, COUNT(*) as count")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();


        // Debugging: Check fetched data
        // dd($start);

        // Generate labels and data
        $labels = [];
        $data = [];
        $current = $start->copy();

        while ($current <= $end) {
            $label = $current->format('M d');
            $dateKey = $current->toDateString();

            $labels[] = $label;
            $data[] = $servicesUsed[$dateKey] ?? 0;

            $current->addDay(); // Iterate day by day
        }

        return [
            'datasets' => [
                [
                    'label' => 'Services Used',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
