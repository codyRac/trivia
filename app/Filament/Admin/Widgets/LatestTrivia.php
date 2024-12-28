<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\TriviaResource;
use App\Models\Trivia;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTrivia extends BaseWidget
{

    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(TriviaResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('used_on', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('answer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wrong_1')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('wrong_2')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('wrong_3')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('used_on')
                ->dateTime()
                ->since()
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('result')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
