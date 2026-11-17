<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** CRUD des caractéristiques (points forts) d'un projet. */
class FeaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'features';

    protected static ?string $title = 'Caractéristiques';

    protected static ?string $modelLabel = 'caractéristique';

    protected static ?string $pluralModelLabel = 'caractéristiques';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-sparkles';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) $ownerRecord->features()->count() ?: null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('icon_class')
                    ->label('Icône Boxicons')
                    ->maxLength(255)
                    ->placeholder('bx bx-lock')
                    ->helperText('Classe trouvée sur boxicons.com, ex : bx bxl-php'),
                Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('icon_class')
                    ->label('')
                    ->html()
                    ->formatStateUsing(fn (?string $state) => $state
                        ? '<i class="' . e($state) . '" style="font-size:1.4rem"></i>'
                        : '')
                    ->width('1%'),
                TextColumn::make('title')
                    ->label('Titre')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(110)
                    ->wrap(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(fn (array $data) => $data + [
                        'sort_order' => (int) $this->getOwnerRecord()->features()->max('sort_order') + 1,
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucune caractéristique')
            ->emptyStateDescription('Les points forts du projet, affichés sur sa page.');
    }
}
