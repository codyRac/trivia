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
        $days = $left- $done;
        $description = null;
        if($days <= 15){
            $description = 'Running out';
        }
        return [
            Stat::make('Triva Left', $left)
                ->icon('heroicon-m-clipboard-document-list'),
            Stat::make('Triva Completed', $done)
                ->icon('heroicon-m-clipboard-document-check'),
            Stat::make('Days before out',$days)
                ->icon('heroicon-m-clock')
                ->description($description)
                ->descriptionIcon('heroicon-m-shield-exclamation')->color('danger'),
       ];
    }
}
