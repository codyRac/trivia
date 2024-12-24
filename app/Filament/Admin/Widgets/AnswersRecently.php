<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Trivia;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AnswersRecently extends ChartWidget
{
    protected static ?string $heading = 'Answer Results';

    protected static ?int $sort = 2;
    protected static string $color = 'success';


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

        $results = Trivia::whereBetween('used_on', [$start, $end])
        ->selectRaw("result, COUNT(*) as count")
        ->groupBy('result')
        ->pluck('count', 'result')
        ->toArray();


        $rightCount = $results['right'] ?? 0;
        $wrongCount = $results['wrong'] ?? 0;
        return [
            'datasets' => [
                [
                    'label' => 'Results',
                    'data' => [$rightCount, $wrongCount],
                ],
            ],
            'labels' => ['Right', 'Wrong'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
