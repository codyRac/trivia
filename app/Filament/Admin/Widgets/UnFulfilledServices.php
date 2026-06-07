<?php

namespace App\Filament\Admin\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use App\Models\Service;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UnFulfilledServices extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {

        $query = Service::query()
        ->whereColumn('times_used', '>', 'fulfilled')
        ;
        return $table
            ->query(
                $query
            )
            ->columns([
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('duration')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                    TextColumn::make('times_used')
                    ->sortable(),
                    TextColumn::make('fulfilled')
                    ->sortable(),

                    IconColumn::make('favorite')
                ->boolean()
                ->sortable(),
            ]);
    }
}
