<?php

namespace App\Filament\Resources\Technologies;

use App\Filament\Resources\Technologies\Pages\ManageTechnologies;
use App\Models\Technology;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class TechnologyResource extends Resource
{
    protected static ?string $model = Technology::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static string|UnitEnum|null $navigationGroup = 'Portfolio';

    protected static ?string $modelLabel = 'technologie';

    protected static ?string $pluralModelLabel = 'technologies';

    protected static ?string $navigationLabel = 'Technologies';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nom')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Select::make('category')
                ->label('Catégorie')
                ->options(Technology::CATEGORIES)
                ->default('dev')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Technology::CATEGORIES[$state] ?? $state)
                    ->color(fn (string $state) => $state === 'design' ? 'warning' : 'info')
                    ->sortable(),
                TextColumn::make('projects_count')
                    ->label('Projets')
                    ->counts('projects')
                    ->badge()
                    ->sortable(),
                TextColumn::make('projects.title')
                    ->label('Utilisée dans')
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options(Technology::CATEGORIES),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTechnologies::route('/'),
        ];
    }
}
