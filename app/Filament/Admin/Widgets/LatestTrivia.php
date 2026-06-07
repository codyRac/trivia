<?php

namespace App\Filament\Admin\Widgets;

use Filament\Tables\Columns\TextColumn;
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

        $query = Trivia::query()
        ->whereNotNull('result')
        ->orderBy('used_on', 'desc');
        return $table
            ->query($query )

            ->defaultPaginationPageOption(5)
            ->defaultSort('used_on', 'desc')
            ->columns([
                TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                    TextColumn::make('question')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('answer')
                    ->searchable(),
                TextColumn::make('wrong_1')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('wrong_2')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('wrong_3')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('used_on')
                ->dateTime()
                ->since()
                ->sortable()
                ->searchable(),
                TextColumn::make('result')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
