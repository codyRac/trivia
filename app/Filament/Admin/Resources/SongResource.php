<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Throwable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\SongResource\Pages\ListSongs;
use App\Filament\Admin\Resources\SongResource\Pages\CreateSong;
use App\Filament\Admin\Resources\SongResource\Pages\EditSong;
use App\Filament\Admin\Resources\SongResource\Pages;
use App\Filament\Admin\Resources\SongResource\RelationManagers;
use App\Models\Song;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use App\Services\SongImportService;


class SongResource extends Resource
{
    protected static ?string $model = Song::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-musical-note';

     public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('artist')
                    ->maxLength(255),
                TextInput::make('album')
                    ->maxLength(255),
                TextInput::make('year')
                    ->numeric(),
                TextInput::make('genre')
                    ->maxLength(255),
                TextInput::make('rating')
                    ->numeric(),
                TextInput::make('spotify_link')
                    ->default(null),
                TextInput::make('apple_music_link')
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('artist')
                    ->searchable(),
                TextColumn::make('album')
                    ->searchable(),
                TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('genre')
                    ->searchable(),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('spotify_link')
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('apple_music_link')
                //     ->sortable(),
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
            ->headerActions([
                Action::make('importCsv')
                    ->label('Import CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->schema([
                        FileUpload::make('csv_file')
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
                            } catch (Throwable $e) {
                                Notification::make()
                                    ->title('Import failed')
                                    ->danger()
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
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
            'index' => ListSongs::route('/'),
            'create' => CreateSong::route('/create'),
            'edit' => EditSong::route('/{record}/edit'),
        ];
    }
}
