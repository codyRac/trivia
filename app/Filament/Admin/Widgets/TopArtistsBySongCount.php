<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Song;
use Illuminate\Support\Facades\DB;

class TopArtistsBySongCount extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Artists by Song Count';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        // Get top 5 artists by song count
        $artistData = Song::select('artist', DB::raw('count(*) as song_count'))
            ->whereNotNull('artist')
            ->groupBy('artist')
            ->orderBy('song_count', 'desc')
            ->limit(5)
            ->get();

        $labels = $artistData->pluck('artist')->toArray();
        $data = $artistData->pluck('song_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Songs',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
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
}
