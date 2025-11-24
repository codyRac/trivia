<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Song;
use Illuminate\Support\Facades\DB;

class TopArtistsByAverageRating extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Artists by Average Rating';
    protected static ?int $sort = 8;

    protected function getData(): array
    {
        // Get top 5 artists by average rating (only include artists with rated songs)
        $artistData = Song::select('artist', DB::raw('AVG(rating) as avg_rating'), DB::raw('count(*) as song_count'))
            ->where('rating', '>', 0)
            ->whereNotNull('artist')
            ->groupBy('artist')
            ->orderBy('avg_rating', 'desc')
            ->limit(5)
            ->get();

        $labels = $artistData->pluck('artist')->map(function ($artist, $index) use ($artistData) {
            $songCount = $artistData[$index]->song_count;
            return $artist . " ({$songCount} songs)";
        })->toArray();

        $data = $artistData->pluck('avg_rating')->map(function ($avg) {
            return round($avg, 2);
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Average Rating',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                    ],
                    'borderColor' => [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)',
                    ],
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
