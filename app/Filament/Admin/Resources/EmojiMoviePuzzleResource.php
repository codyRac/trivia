<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\EmojiMoviePuzzleResource\Pages\ListEmojiMoviePuzzles;
use App\Filament\Admin\Resources\EmojiMoviePuzzleResource\Pages\CreateEmojiMoviePuzzle;
use App\Filament\Admin\Resources\EmojiMoviePuzzleResource\Pages\EditEmojiMoviePuzzle;
use App\Filament\Admin\Resources\EmojiMoviePuzzleResource\Pages;
use App\Filament\Admin\Resources\EmojiMoviePuzzleResource\RelationManagers;
use App\Models\EmojiMoviePuzzle;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmojiMoviePuzzleResource extends Resource
{
    protected static ?string $model = EmojiMoviePuzzle::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-face-smile';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('emojis')
                    ->label('Emoji clue')
                    ->placeholder('🍕🐢🗽')
                    ->required()
                    ->maxLength(255),
                TextInput::make('correct_answer')
                    ->label('Correct movie')
                    ->placeholder('Teenage Mutant Ninja Turtles')
                    ->required()
                    ->maxLength(255),
               Section::make('Wrong Answers (6)')
                    ->schema([
                        TextInput::make('wrong_answer_1')
                            ->label('Wrong answer 1')
                            ->required(),
                        TextInput::make('wrong_answer_2')
                            ->label('Wrong answer 2')
                            ->required(),
                        TextInput::make('wrong_answer_3')
                            ->label('Wrong answer 3')
                            ->required(),
                        TextInput::make('wrong_answer_4')
                            ->label('Wrong answer 4')
                            ->required(),
                        TextInput::make('wrong_answer_5')
                            ->label('Wrong answer 5')
                            ->required(),
                        TextInput::make('wrong_answer_6')
                            ->label('Wrong answer 6')
                            ->required(),
                    ])
                    ->columns(2),
                Toggle::make('used')
                    ->label('Used')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('emojis')
                    ->searchable(),
                TextColumn::make('correct_answer')
                    ->searchable(),
                TextColumn::make('wrong_answer_1')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('wrong_answer_2')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('wrong_answer_3')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('wrong_answer_4')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('wrong_answer_5')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('wrong_answer_6')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('used')
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
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListEmojiMoviePuzzles::route('/'),
            'create' => CreateEmojiMoviePuzzle::route('/create'),
            'edit' => EditEmojiMoviePuzzle::route('/{record}/edit'),
        ];
    }
}
