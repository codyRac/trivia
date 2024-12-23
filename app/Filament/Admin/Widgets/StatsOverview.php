<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Trivia;

class StatsOverview extends BaseWidget
{

    protected static ?int $sort = 3;

    protected function getStats(): array
    {

        $left = Trivia::where('used',0)->count();
        $done = Trivia::where('used',1)->count();

        return [
            Stat::make('Triva Left', $left)
                ->descriptionIcon('heroicon-m-question-mark-circle'),
            Stat::make('Triva Completed', $done)
                ->descriptionIcon('heroicon-m-question-mark-circle'),
            Stat::make('Days before out',$left- $done)
                ->descriptionIcon('heroicon-m-question-mark-circle'),


       ];
    }
}
