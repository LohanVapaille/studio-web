<?php

namespace App\Filament\Resources\LovaApps\Pages;

use App\Filament\Resources\LovaApps\LovaAppResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditLovaApp extends EditRecord
{
    protected static string $resource = LovaAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open')
                ->label("Ouvrir l'app")
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(fn () => $this->record->url, shouldOpenInNewTab: true)
                ->visible(fn () => filled($this->record->url)),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
