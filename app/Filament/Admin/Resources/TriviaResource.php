<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Admin\Resources\TriviaResource\Pages\ListTrivia;
use App\Filament\Admin\Resources\TriviaResource\Pages\CreateTrivia;
use App\Filament\Admin\Resources\TriviaResource\Pages\ViewTrivia;
use App\Filament\Admin\Resources\TriviaResource\Pages\EditTrivia;
use App\Filament\Admin\Resources\TriviaResource\Pages;
use App\Filament\Admin\Resources\TriviaResource\RelationManagers;
use App\Models\Trivia;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TriviaResource extends Resource
{
    protected static ?string $model = Trivia::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-light-bulb';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('question')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                TextInput::make('answer')
                    ->required()
                    ->maxLength(255),
                TextInput::make('wrong_1')
                    ->required()
                    ->maxLength(255),
                TextInput::make('wrong_2')
                    ->required()
                    ->maxLength(255),
                TextInput::make('wrong_3')
                    ->required()
                    ->maxLength(255),
                Select::make('result')->options([
                        'right' => 'Right',
                        'wrong' => 'Wrong',
                    ]),
                Toggle::make('used'),
                DateTimePicker::make('used_on')
                ->timezone('America/Los_Angeles')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('answer')
                    ->searchable(),
                TextColumn::make('wrong_1')
                    ->searchable(),
                TextColumn::make('wrong_2')
                    ->searchable(),
                TextColumn::make('wrong_3')
                    ->searchable(),
                IconColumn::make('used')
                ->boolean()
                ->sortable(),
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
            ])
            ->filters([
                Filter::make('used')
                ->label('Already Used')
                ->query(fn (Builder $query): Builder => $query->where('used', true)),
                SelectFilter::make('result')
                ->multiple()
                ->options([
                    'right' => 'Right',
                    'wrong' => 'Wrong',
                ]),

                SelectFilter::make('category')
                ->multiple()
                ->options([
                    'The Office' => 'The Office',
                    'Red Dead Redemption 2' => 'Red Dead Redemption 2',
                    'Half Moon Run' => 'Half Moon Run',
                    'Taylor Swift'=> 'Taylor Swift',

                ]),
                TrashedFilter::make(),


            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
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
            'index' => ListTrivia::route('/'),
            'create' => CreateTrivia::route('/create'),
            'view' => ViewTrivia::route('/{record}'),
            'edit' => EditTrivia::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
