<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmojiMoviePuzzleResource\Pages;
use App\Filament\Admin\Resources\EmojiMoviePuzzleResource\RelationManagers;
use App\Models\EmojiMoviePuzzle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmojiMoviePuzzleResource extends Resource
{
    protected static ?string $model = EmojiMoviePuzzle::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('emojis')
                    ->label('Emoji clue')
                    ->placeholder('🍕🐢🗽')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('correct_answer')
                    ->label('Correct movie')
                    ->placeholder('Teenage Mutant Ninja Turtles')
                    ->required()
                    ->maxLength(255),
               Forms\Components\Section::make('Wrong Answers (6)')
                    ->schema([
                        Forms\Components\TextInput::make('wrong_answer_1')
                            ->label('Wrong answer 1')
                            ->required(),
                        Forms\Components\TextInput::make('wrong_answer_2')
                            ->label('Wrong answer 2')
                            ->required(),
                        Forms\Components\TextInput::make('wrong_answer_3')
                            ->label('Wrong answer 3')
                            ->required(),
                        Forms\Components\TextInput::make('wrong_answer_4')
                            ->label('Wrong answer 4')
                            ->required(),
                        Forms\Components\TextInput::make('wrong_answer_5')
                            ->label('Wrong answer 5')
                            ->required(),
                        Forms\Components\TextInput::make('wrong_answer_6')
                            ->label('Wrong answer 6')
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Toggle::make('used')
                    ->label('Used')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('emojis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('correct_answer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wrong_answer_1')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('wrong_answer_2')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('wrong_answer_3')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('wrong_answer_4')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('wrong_answer_5')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('wrong_answer_6')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('used')
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEmojiMoviePuzzles::route('/'),
            'create' => Pages\CreateEmojiMoviePuzzle::route('/create'),
            'edit' => Pages\EditEmojiMoviePuzzle::route('/{record}/edit'),
        ];
    }
}
