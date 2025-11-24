<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Song;
use Illuminate\Support\Facades\DB;

class AverageRatingByGenreChart extends ChartWidget
{
    protected static ?string $heading = 'Average Rating per Genre';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        // Get average rating per genre
        $genreData = Song::select('genre', DB::raw('AVG(rating) as avg_rating'))
            ->where('rating', '>', 0)
            ->whereNotNull('genre')
            ->groupBy('genre')
            ->orderBy('avg_rating', 'desc')
            ->get();

        $labels = $genreData->pluck('genre')->toArray();
        $data = $genreData->pluck('avg_rating')->map(function ($avg) {
            return round($avg, 2);
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Average Rating',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 10,
                ],
            ],
        ];
    }
}
