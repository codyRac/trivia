<?php

namespace App\Filament\Admin\Widgets;

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
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('times_used')
                    ->sortable(),
                    Tables\Columns\TextColumn::make('fulfilled')
                    ->sortable(),

                    Tables\Columns\IconColumn::make('favorite')
                ->boolean()
                ->sortable(),
            ]);
    }
}
