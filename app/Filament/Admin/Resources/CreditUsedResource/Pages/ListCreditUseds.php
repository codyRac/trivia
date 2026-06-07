<?php

namespace App\Filament\Admin\Resources\CreditUsedResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\CreditUsedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreditUseds extends ListRecords
{
    protected static string $resource = CreditUsedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
