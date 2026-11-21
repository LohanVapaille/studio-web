<?php

namespace App\Filament\Resources\LovaApps\Pages;

use App\Filament\Resources\LovaApps\LovaAppResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLovaApps extends ListRecords
{
    protected static string $resource = LovaAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
