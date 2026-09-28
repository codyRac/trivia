<?php

namespace App\Filament\Admin\Resources;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Admin\Resources\FlagDayResource\Pages\ListFlagDays;
use App\Models\Flag;
use App\Models\FlagDay;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class FlagDayResource extends Resource
{
    protected static ?string $model = FlagDay::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Flag Days';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                ImageColumn::make('new_flag')
                    ->label('')
                    ->state(fn (FlagDay $record) => $record->flag ? "https://flagcdn.com/w80/{$record->flag->code}.png" : null)
                    ->imageHeight(24),
                TextColumn::make('flag.name')
                    ->label('New flag')
                    ->placeholder('All collected')
                    ->searchable(),
                TextColumn::make('score')
                    ->state(function (FlagDay $record) {
                        $total = Flag::whereDate('learned_on', '<=', $record->date)->count();
                        return "{$record->correct} / {$total}";
                    })
                    ->description(fn (FlagDay $record) => "{$record->answered} answered"),
                TextColumn::make('status')
                    ->state(fn (FlagDay $record) => match (true) {
                        (bool) $record->completed_at => 'Done',
                        $record->answered > 0 => 'In progress',
                        default => 'Not started',
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Done' => 'success',
                        'In progress' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->timezone('America/Los_Angeles')
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFlagDays::route('/'),
        ];
    }
}
