<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Song;

class HighRatedSongsCount extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $highRatedCount = Song::where('rating', '>=', 8)->count();
        $totalRated = Song::where('rating', '>', 0)->count();

        return [
            Stat::make('High Rated Songs', $highRatedCount)
                ->description('Songs rated 8 or higher')
                ->descriptionIcon('heroicon-m-star')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            Stat::make('Total Rated Songs', $totalRated)
                ->description('All songs with ratings')
                ->descriptionIcon('heroicon-m-musical-note')
                ->color('info'),
            Stat::make('High Rating %', $totalRated > 0 ? round(($highRatedCount / $totalRated) * 100, 1) . '%' : '0%')
                ->description('Percentage of high ratings')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
