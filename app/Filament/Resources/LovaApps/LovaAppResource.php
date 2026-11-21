<?php

namespace App\Filament\Resources\LovaApps;

use App\Filament\Resources\LovaApps\Pages\CreateLovaApp;
use App\Filament\Resources\LovaApps\Pages\EditLovaApp;
use App\Filament\Resources\LovaApps\Pages\ListLovaApps;
use App\Filament\Resources\LovaApps\Schemas\LovaAppForm;
use App\Filament\Resources\LovaApps\Tables\LovaAppsTable;
use App\Models\LovaApp;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LovaAppResource extends Resource
{
    protected static ?string $model = LovaApp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Lova';

    protected static ?string $modelLabel = 'application';

    protected static ?string $pluralModelLabel = 'applications Lova';

    protected static ?string $navigationLabel = 'Applications';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LovaAppForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LovaAppsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) LovaApp::where('is_visible', true)->count() ?: null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLovaApps::route('/'),
            'create' => CreateLovaApp::route('/create'),
            'edit' => EditLovaApp::route('/{record}/edit'),
        ];
    }
}
