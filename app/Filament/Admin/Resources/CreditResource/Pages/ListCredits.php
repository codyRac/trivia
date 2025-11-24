<?php

namespace App\Filament\Admin\Resources\CreditResource\Pages;

use App\Filament\Admin\Resources\CreditResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Widgets\{Credits,};

class ListCredits extends ListRecords
{
    protected static string $resource = CreditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            Credits::class,
        ];
    }
}
