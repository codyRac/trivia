<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\FlagResource\Pages\ListFlags;
use App\Filament\Admin\Resources\FlagResource\Pages\EditFlag;
use App\Models\Flag;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FlagResource extends Resource
{
    protected static ?string $model = Flag::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-flag';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Country code')
                    ->helperText('ISO 3166-1 alpha-2, lowercase (used for the flag image)')
                    ->required()
                    ->length(2),
                TextInput::make('learned_order')
                    ->label('Collected #')
                    ->numeric()
                    ->helperText('Leave empty if not collected yet'),
                DatePicker::make('learned_on')
                    ->label('Collected on'),
                TextInput::make('times_correct')
                    ->numeric()
                    ->required(),
                TextInput::make('times_wrong')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('flag')
                    ->state(fn (Flag $record) => "https://flagcdn.com/w80/{$record->code}.png")
                    ->imageHeight(24),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('learned_order')
                    ->label('Collected #')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('learned_on')
                    ->label('Collected on')
                    ->date()
                    ->sortable()
                    ->placeholder('Not yet'),
                TextColumn::make('times_correct')
                    ->label('Correct')
                    ->sortable(),
                TextColumn::make('times_wrong')
                    ->label('Wrong')
                    ->sortable(),
                TextColumn::make('accuracy')
                    ->state(function (Flag $record) {
                        $total = $record->times_correct + $record->times_wrong;
                        return $total ? round($record->times_correct / $total * 100) . '%' : null;
                    })
                    ->placeholder('—')
                    ->badge()
                    ->color(fn (?string $state) => match (true) {
                        $state === null => 'gray',
                        (int) $state >= 80 => 'success',
                        (int) $state >= 50 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->defaultSort('learned_order', 'desc')
            ->filters([
                TernaryFilter::make('collected')
                    ->placeholder('All flags')
                    ->trueLabel('Collected')
                    ->falseLabel('Not collected yet')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('learned_order'),
                        false: fn (Builder $query) => $query->whereNull('learned_order'),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFlags::route('/'),
            'edit' => EditFlag::route('/{record}/edit'),
        ];
    }
}
