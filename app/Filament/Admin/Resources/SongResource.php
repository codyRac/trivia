<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SongResource\Pages;
use App\Filament\Admin\Resources\SongResource\RelationManagers;
use App\Models\Song;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use App\Services\SongImportService;


class SongResource extends Resource
{
    protected static ?string $model = Song::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

     public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('artist')
                    ->maxLength(255),
                Forms\Components\TextInput::make('album')
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->numeric(),
                Forms\Components\TextInput::make('genre')
                    ->maxLength(255),
                Forms\Components\TextInput::make('rating')
                    ->numeric(),
                Forms\Components\TextInput::make('spotify_link')
                    ->required()
                    ->default(0),
                Forms\Components\TextInput::make('apple_music_link')
                    ->required()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('artist')
                    ->searchable(),
                Tables\Columns\TextColumn::make('album')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('genre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('spotify_link')
                    ->sortable(),
                Tables\Columns\TextColumn::make('apple_music_link')
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
            ->headerActions([
    Action::make('importCsv')
        ->label('Import CSV')
        ->icon('heroicon-o-arrow-up-tray')
        ->form([
            Forms\Components\FileUpload::make('csv_file')
                ->label('CSV file')
                ->disk('local')              // adjust if needed
                ->directory('imports/songs') // optional
                ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                ->required(),
        ])
        // Note the type-hinted SongImportService — Laravel will resolve it
        ->action(function (array $data, SongImportService $importer): void {
                try {
                    $path = Storage::disk('local')->path($data['csv_file']);

                    $created = $importer->importFromCsv($path);

                    Notification::make()
                        ->title('Import complete')
                        ->success()
                        ->body("Imported {$created} songs from CSV.")
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Import failed')
                        ->danger()
                        ->body($e->getMessage())
                        ->send();
                }
            }),
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
            'index' => Pages\ListSongs::route('/'),
            'create' => Pages\CreateSong::route('/create'),
            'edit' => Pages\EditSong::route('/{record}/edit'),
        ];
    }
}
