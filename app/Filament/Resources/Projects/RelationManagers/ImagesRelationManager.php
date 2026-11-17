<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Models\ProjectImage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** CRUD de la galerie d'images d'un projet (ajout multiple, légende, ordre). */
class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Galerie';

    protected static ?string $modelLabel = 'image';

    protected static ?string $pluralModelLabel = 'images';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-photo';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) $ownerRecord->images()->count() ?: null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            ProjectForm::image('path')
                ->label('Image')
                ->required()
                ->columnSpanFull(),
            TextInput::make('caption')
                ->label('Légende')
                ->maxLength(255)
                ->helperText('Optionnelle. Sert aussi de texte alternatif.')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->contentGrid(['md' => 3, 'xl' => 4])
            ->columns([
                Stack::make([
                    ImageColumn::make('path')
                        ->state(fn (ProjectImage $record) => $record->url)
                        ->imageHeight(160)
                        ->extraImgAttributes(['style' => 'width:100%;object-fit:cover;border-radius:.5rem']),
                    TextColumn::make('caption')
                        ->placeholder('Sans légende')
                        ->color('gray')
                        ->size('sm'),
                ])->space(2),
            ])
            ->headerActions([
                Action::make('addMany')
                    ->label('Ajouter des images')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading('Ajouter des images à la galerie')
                    ->schema([
                        ProjectForm::image('paths')
                            ->label('Images')
                            ->multiple()
                            ->reorderable()
                            ->required()
                            ->maxFiles(30),
                    ])
                    ->action(function (array $data) {
                        $project = $this->getOwnerRecord();
                        $order = (int) $project->images()->max('sort_order');

                        foreach ($data['paths'] as $path) {
                            $project->images()->create(['path' => $path, 'sort_order' => ++$order]);
                        }

                        Notification::make()->success()->title(count($data['paths']) . ' image(s) ajoutée(s)')->send();
                    }),
                CreateAction::make()
                    ->label('Une image avec légende')
                    ->color('gray')
                    ->mutateDataUsing(fn (array $data) => $data + [
                        'sort_order' => (int) $this->getOwnerRecord()->images()->max('sort_order') + 1,
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
            ->paginated([12, 24, 48, 'all'])
            ->emptyStateHeading('Aucune image')
            ->emptyStateDescription('Ajoutez les captures du projet : elles forment la galerie de sa page.');
    }
}
