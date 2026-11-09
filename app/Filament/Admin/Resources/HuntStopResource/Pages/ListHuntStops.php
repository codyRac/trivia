<?php

namespace App\Filament\Admin\Resources\HuntStopResource\Pages;

use App\Filament\Admin\Resources\HuntStopResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHuntStops extends ListRecords
{
    protected static string $resource = HuntStopResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
