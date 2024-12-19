<?php

namespace App\Filament\Admin\Resources\TriviaResource\Pages;

use App\Filament\Admin\Resources\TriviaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTrivia extends CreateRecord
{
    protected static string $resource = TriviaResource::class;
}
