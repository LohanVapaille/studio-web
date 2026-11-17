<?php

namespace App\Filament\Resources\Timeline;

use App\Filament\Resources\Timeline\Pages\ManageTimelineEntries;
use App\Models\TimelineEntry;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Parcours (formation, expériences) affiché sur le portfolio. */
class TimelineEntryResource extends Resource
{
    protected static ?string $model = TimelineEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Portfolio';

    protected static ?string $modelLabel = 'étape';

    protected static ?string $pluralModelLabel = 'parcours';

    protected static ?string $navigationLabel = 'Parcours';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $slug = 'parcours';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label('Intitulé')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('institution')
                    ->label('Établissement / entreprise')
                    ->maxLength(255),
                TextInput::make('period_label')
                    ->label('Période affichée')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Septembre 2024'),
                DatePicker::make('sorted_at')
                    ->label('Date (pour le tri)')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                Toggle::make('is_published')
                    ->label('Visible')
                    ->default(true)
                    ->inline(false),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sorted_at', 'desc')
            ->columns([
                TextColumn::make('period_label')
                    ->label('Période')
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('sorted_at', $direction)),
                TextColumn::make('title')
                    ->label('Intitulé')
                    ->searchable()
                    ->wrap()
                    ->description(fn (TimelineEntry $record) => $record->institution),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(80)
                    ->wrap()
                    ->toggleable(),
                ToggleColumn::make('is_published')
                    ->label('Visible'),
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
            'index' => ManageTimelineEntries::route('/'),
        ];
    }
}
