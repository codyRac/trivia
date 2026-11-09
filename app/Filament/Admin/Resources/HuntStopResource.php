<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HuntStopResource\Pages\CreateHuntStop;
use App\Filament\Admin\Resources\HuntStopResource\Pages\EditHuntStop;
use App\Filament\Admin\Resources\HuntStopResource\Pages\ListHuntStops;
use App\Models\HuntStop;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HuntStopResource extends Resource
{
    protected static ?string $model = HuntStop::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Hunt Stops & Clues';

    protected static ?string $modelLabel = 'hunt stop';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('title')
                    ->helperText('Only shown to her after the stop is completed')
                    ->required()
                    ->maxLength(255),
                TextInput::make('position')
                    ->label('Order')
                    ->numeric()
                    ->required()
                    ->default(fn () => (HuntStop::max('position') ?? 0) + 1),
                TextInput::make('extra')
                    ->label('Extra note')
                    ->helperText('Shown with the first clue, e.g. "You will have a friend joining for this spot"')
                    ->maxLength(255),
                Textarea::make('clues.0')
                    ->label('Clue 1 — 10 points')
                    ->rows(4)
                    ->required(),
                Textarea::make('clues.1')
                    ->label('Clue 2 — 7 points')
                    ->rows(4)
                    ->required(),
                Textarea::make('clues.2')
                    ->label('Clue 3 — 4 points')
                    ->rows(4)
                    ->required(),
                TextInput::make('answer')
                    ->label('Answer — 1 point')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('position')
                    ->label('#'),
                TextColumn::make('title'),
                TextColumn::make('extra')
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('clues_revealed')
                    ->label('Revealed')
                    ->formatStateUsing(fn (int $state) => $state >= 4 ? 'Answer' : "Clue {$state}"),
                TextColumn::make('points')
                    ->placeholder('—'),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->paginated(false)
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHuntStops::route('/'),
            'create' => CreateHuntStop::route('/create'),
            'edit' => EditHuntStop::route('/{record}/edit'),
        ];
    }
}
