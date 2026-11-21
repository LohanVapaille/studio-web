<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\RichText;
use App\Models\Project;
use App\Models\Technology;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    /** Images enregistrées dans public/uploads/projets (chemin relatif à public/, comme « img/WEB/x.jpg »). */
    public static function image(string $name): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk('site')
            ->directory('uploads/projets')
            ->visibility('public')
            ->imageEditor()
            ->maxSize(8192);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // ---------- Colonne principale ----------
                Group::make([
                    Section::make('Projet')
                        ->schema([
                            ToggleButtons::make('type')
                                ->label('Type de projet')
                                ->options(Project::TYPES)
                                ->icons(['web' => 'heroicon-o-code-bracket', 'graphic' => 'heroicon-o-paint-brush'])
                                ->default('web')
                                ->inline()
                                ->required()
                                ->live()
                                ->helperText('Lovaweb n\'affiche que les projets web.'),

                            TextInput::make('title')
                                ->label('Titre')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                    if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                                        $set('slug', Str::slug((string) $state));
                                    }
                                }),

                            TextInput::make('slug')
                                ->label('Adresse de la page')
                                ->prefix(url('/projets') . '/')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->rule('alpha_dash')
                                ->helperText('Vide = générée depuis le titre.'),

                            Textarea::make('short_description')
                                ->label('Description courte')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Affichée sur les cartes projet et en introduction de la page.'),

                            RichText::make('presentation')
                                ->label('Présentation détaillée')
                                ->helperText('Mise en page affichée à l’identique sur Lovaweb et sur le portfolio. H2 = grandes parties, H3 = sous-parties, « Lead » = introduction, « Citation » = encadré.'),
                        ]),

                    Section::make('Visuels')
                        ->description('Les caractéristiques et la galerie se gèrent dans les onglets sous ce formulaire.')
                        ->columns(2)
                        ->schema([
                            self::image('main_image')
                                ->label('Image principale / miniature')
                                ->helperText('Carte du projet.'),

                            self::image('mockup_image')
                                ->label(fn (Get $get) => $get('type') === 'graphic' ? 'Visuel de présentation' : 'Mockup')
                                ->helperText('Grande image en haut de la page projet.'),
                        ]),
                ])->columnSpan(['lg' => 2]),

                // ---------- Colonne latérale ----------
                Group::make([
                    Section::make('Affichage')
                        ->schema([
                            Toggle::make('show_on_lovaweb')
                                ->label('Visible sur Lovaweb')
                                ->default(true)
                                ->live()
                                ->disabled(fn (Get $get) => $get('type') !== 'web')
                                ->helperText(fn (Get $get) => $get('type') !== 'web' ? 'Réservé aux projets web.' : null),

                            Toggle::make('is_featured')
                                ->label("Mis en avant sur l'accueil Lovaweb")
                                ->visible(fn (Get $get) => $get('type') === 'web' && $get('show_on_lovaweb')),

                            Toggle::make('show_on_portfolio')
                                ->label('Visible sur le portfolio')
                                ->live(),

                            Toggle::make('is_featured_portfolio')
                                ->label('Mis en avant sur le portfolio')
                                ->helperText('Affiché d\'office, sans cliquer sur « voir plus ».')
                                ->visible(fn (Get $get) => $get('show_on_portfolio')),
                        ]),

                    Section::make('Détails')
                        ->schema([
                            TextInput::make('url')
                                ->label('Adresse du site')
                                ->url()
                                ->maxLength(255)
                                ->placeholder('https://...'),

                            TextInput::make('category')
                                ->label('Catégorie')
                                ->maxLength(255)
                                ->datalist(fn () => self::distinct('category')),

                            TextInput::make('date_label')
                                ->label('Date')
                                ->maxLength(255)
                                ->placeholder('Avril 2026'),

                            TextInput::make('role')
                                ->label('Mon rôle')
                                ->maxLength(255),

                            TextInput::make('duration')
                                ->label('Durée')
                                ->maxLength(255)
                                ->placeholder('2 mois'),

                            TextInput::make('context')
                                ->label('Contexte')
                                ->maxLength(255)
                                ->datalist(fn () => self::distinct('context'))
                                ->placeholder('Projet pour un client'),

                            TextInput::make('status')
                                ->label('Statut')
                                ->maxLength(255)
                                ->datalist(fn () => self::distinct('status'))
                                ->placeholder('Actif / Utilisable'),

                            TagsInput::make('tags')
                                ->label('Étiquettes')
                                ->placeholder('Ajouter une étiquette')
                                ->helperText('Ex. : Logos, Réseaux sociaux. Entrée pour valider.'),
                        ]),

                    Section::make('Technologies')
                        ->schema([
                            Select::make('technologies')
                                ->hiddenLabel()
                                ->relationship('technologies', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label('Nom')
                                        ->required()
                                        ->unique('technologies', 'name')
                                        ->maxLength(255),
                                    Select::make('category')
                                        ->label('Catégorie')
                                        ->options(Technology::CATEGORIES)
                                        ->default('dev')
                                        ->required(),
                                ]),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    /** Valeurs déjà utilisées, proposées en autocomplétion. */
    private static function distinct(string $column): array
    {
        return Project::query()->whereNotNull($column)->distinct()->orderBy($column)->pluck($column)->all();
    }
}
