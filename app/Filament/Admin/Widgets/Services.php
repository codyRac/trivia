<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Service;

class Services extends BaseWidget
{

    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $left = Service::where('times_used',0)->count();
        $done = Service::where('times_used','>=', 1)->count();

        return [
            Stat::make('Unused Services', $left)
                ->descriptionIcon('heroicon-m-question-mark-circle'),
            Stat::make('Number of Used Servcies', $done)
                ->descriptionIcon('heroicon-m-question-mark-circle'),



       ];
    }
}
