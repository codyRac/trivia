<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TriviaResource\Pages;
use App\Filament\Admin\Resources\TriviaResource\RelationManagers;
use App\Models\Trivia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TriviaResource extends Resource
{
    protected static ?string $model = Trivia::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('question')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('answer')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('wrong_1')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('wrong_2')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('wrong_3')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('result')->options([
                        'right' => 'Right',
                        'wrong' => 'Wrong',
                    ]),
                Forms\Components\Toggle::make('used'),
                Forms\Components\DateTimePicker::make('used_on')
                ->timezone('America/Los_Angeles')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('answer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wrong_1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wrong_2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wrong_3')
                    ->searchable(),
                Tables\Columns\IconColumn::make('used')
                ->boolean()
                ->sortable(),
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
            ])
            ->filters([
                Tables\Filters\Filter::make('used')
                ->label('Already Used')
                ->query(fn (Builder $query): Builder => $query->where('used', true)),
                Tables\Filters\SelectFilter::make('result')
                ->multiple()
                ->options([
                    'right' => 'Right',
                    'wrong' => 'Wrong',
                ]),

                Tables\Filters\SelectFilter::make('category')
                ->multiple()
                ->options([
                    'The Office' => 'The Office',
                    'Red Dead Redemption 2' => 'Red Dead Redemption 2',
                    'Half Moon Run' => 'Half Moon Run',
                    'Taylor Swift'=> 'Taylor Swift',

                ]),
                Tables\Filters\TrashedFilter::make(),


            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListTrivia::route('/'),
            'create' => Pages\CreateTrivia::route('/create'),
            'view' => Pages\ViewTrivia::route('/{record}'),
            'edit' => Pages\EditTrivia::route('/{record}/edit'),
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
