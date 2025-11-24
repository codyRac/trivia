<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Song;
use Illuminate\Support\Facades\DB;

class RatingsDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Ratings Distribution';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Get count of songs for each rating (1-10)
        $ratings = Song::select('rating', DB::raw('count(*) as count'))
            ->where('rating', '>', 0)
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Ensure all ratings 1-10 are represented
        $data = [];
        for ($i = 1; $i <= 10; $i++) {
            $data[] = $ratings[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Number of Songs',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(255, 205, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(201, 203, 207, 0.7)',
                        'rgba(255, 99, 255, 0.7)',
                        'rgba(99, 255, 132, 0.7)',
                        'rgba(132, 99, 255, 0.7)',
                    ],
                ],
            ],
            'labels' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
