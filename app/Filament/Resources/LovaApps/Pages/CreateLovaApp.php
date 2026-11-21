<?php

namespace App\Filament\Resources\LovaApps\Pages;

use App\Filament\Resources\LovaApps\LovaAppResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLovaApp extends CreateRecord
{
    protected static string $resource = LovaAppResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
