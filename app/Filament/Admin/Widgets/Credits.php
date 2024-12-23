<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Credit;
class Credits extends BaseWidget
{

    protected static ?int $sort = 1;
    protected function getStats(): array
    {
            $credit = Credit::find(1);

        return [
            Stat::make('Credits', $credit->credits)
                ->description('Credits Left')
                ->icon('heroicon-m-credit-card')
                ->color('success'),
            Stat::make('Spent', $credit->spent)
                ->description('Credits Spent')
                ->icon('heroicon-m-currency-dollar')
                ->color('danger'),
            Stat::make('Earned', $credit->earned)
                ->description('Credits Earned')
                ->icon('heroicon-m-banknotes') ->color('info'),

       ];
    }
}
