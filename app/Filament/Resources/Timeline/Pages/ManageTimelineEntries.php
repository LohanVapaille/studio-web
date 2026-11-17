<?php

namespace App\Filament\Resources\Timeline\Pages;

use App\Filament\Resources\Timeline\TimelineEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTimelineEntries extends ManageRecords
{
    protected static string $resource = TimelineEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
