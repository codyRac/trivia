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



        $mostUsedService = Service::where('times_used', '>=', 1)
            ->orderBy('times_used', 'desc')
            ->first();

            // Define default values for "Most Used Service" stat
        $title = $mostUsedService?->title ?? 'None'; // Fallback to 'None' if no service found
        $times_used = $mostUsedService?->times_used ?? 0;

        return [
            Stat::make('Most Used Servcies:', $times_used)
                ->description($title),
            Stat::make('Unused Services', $left)
                ->descriptionIcon('heroicon-m-question-mark-circle'),
            Stat::make('Number of Used Servcies', $done)
                ->descriptionIcon('heroicon-m-question-mark-circle'),




       ];
    }
}
