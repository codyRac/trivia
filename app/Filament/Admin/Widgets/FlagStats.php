<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Flag;
use App\Models\FlagDay;
use Carbon\Carbon;

class FlagStats extends BaseWidget
{

    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $collected = Flag::whereNotNull('learned_order')->count();
        $all = Flag::count();

        $today = FlagDay::whereDate('date', Carbon::today('America/Los_Angeles')->toDateString())->first();
        $todayTotal = $collected;
        $todayStatus = match (true) {
            !$today => 'Not started',
            (bool) $today->completed_at => "Done ({$today->correct}/{$todayTotal})",
            default => "In progress ({$today->answered}/{$todayTotal})",
        };

        $correct = Flag::sum('times_correct');
        $answered = $correct + Flag::sum('times_wrong');
        $accuracy = $answered ? round($correct / $answered * 100) . '%' : '—';

        return [
            Stat::make('Flags Collected', "{$collected} / {$all}")
                ->icon('heroicon-m-flag')
                ->description(($all - $collected) . ' days of new flags left'),
            Stat::make("Today's Flag Round", $todayStatus)
                ->icon('heroicon-m-calendar-days')
                ->color($today?->completed_at ? 'success' : 'warning'),
            Stat::make('Flag Accuracy', $accuracy)
                ->icon('heroicon-m-check-badge')
                ->description("{$correct} of {$answered} answers correct"),
       ];
    }
}
