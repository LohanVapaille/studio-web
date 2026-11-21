<?php

namespace App\Filament\Resources\LovaApps\Schemas;

use App\Filament\Support\RichText;
use App\Models\LovaApp;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LovaAppForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // ---------- Colonne principale ----------
                Tabs::make()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Présentation')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('LovaTrip')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                                            $set('slug', Str::slug((string) $state));
                                        }
                                    }),

                                TextInput::make('slug')
                                    ->label('Identifiant')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->rule('alpha_dash')
                                    ->helperText('Vide = généré depuis le nom.'),

                                TextInput::make('tagline')
                                    ->label('Accroche')
                                    ->maxLength(120)
                                    ->placeholder("Organisez vos voyages en 2 minutes grâce à l'IA")
                                    ->helperText('Une phrase : le problème que l\'app résout. Affichée sur la carte.'),

                                TagsInput::make('features')
                                    ->label('Points forts')
                                    ->placeholder('Ajouter un point fort')
                                    ->helperText('3 maximum conseillés, ex. : Itinéraire IA, Budget partagé. Entrée pour valider.'),

                                RichText::make('description')
                                    ->label('Description détaillée'),
                            ]),

                        Tab::make('Offre & liens')
                            ->icon('heroicon-o-link')
                            ->columns(2)
                            ->schema([
                                TextInput::make('url')
                                    ->label("Adresse de l'app")
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://lovatrip.fr')
                                    ->columnSpanFull(),

                                Select::make('pricing_model')
                                    ->label('Modèle économique')
                                    ->options(LovaApp::PRICING_MODELS)
                                    ->default('free')
                                    ->required()
                                    ->native(false),

                                TextInput::make('price_label')
                                    ->label('Prix affiché')
                                    ->maxLength(60)
                                    ->placeholder('Dès 4,99 €/mois'),

                                TextInput::make('cta_label')
                                    ->label('Texte du bouton')
                                    ->maxLength(40)
                                    ->placeholder('Essayer gratuitement')
                                    ->helperText('Vide = « Ouvrir ».'),

                                DatePicker::make('launched_at')
                                    ->label('Date de lancement')
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),

                                TextInput::make('admin_url')
                                    ->label("Back-office de l'app")
                                    ->url()
                                    ->maxLength(255),

                                TextInput::make('repo_url')
                                    ->label('Dépôt Git')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://github.com/…'),
                            ]),

                        Tab::make('Pilotage')
                            ->icon('heroicon-o-chart-bar')
                            ->badge('privé')
                            ->schema([
                                Section::make('Coûts mensuels')
                                    ->description('Jamais affiché sur le site. Le total se calcule à partir du détail.')
                                    ->compact()
                                    ->schema([
                                        Repeater::make('cost_items')
                                            ->hiddenLabel()
                                            ->schema([
                                                TextInput::make('label')
                                                    ->label('Poste')
                                                    ->required()
                                                    ->placeholder('Hébergement, API OpenAI, domaine…')
                                                    ->datalist(['Hébergement', 'Nom de domaine', 'API IA', 'Base de données', 'E-mails transactionnels', 'Stockage', 'Stripe']),
                                                TextInput::make('amount')
                                                    ->label('€ / mois')
                                                    ->numeric()
                                                    ->required()
                                                    ->suffix('€'),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Ajouter un coût')
                                            ->reorderable(false)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Set $set, ?array $state) => $set(
                                                'monthly_cost',
                                                collect($state ?? [])->sum(fn($item) => (float) ($item['amount'] ?? 0)) ?: null,
                                            )),

                                        TextInput::make('monthly_cost')
                                            ->label('Total')
                                            ->numeric()
                                            ->suffix('€ / mois')
                                            ->disabled(fn(Get $get) => filled($get('cost_items')))
                                            ->dehydrated(),
                                    ]),

                                Section::make('Indicateurs')
                                    ->compact()
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('mrr')
                                            ->label('Revenu mensuel (MRR)')
                                            ->numeric()
                                            ->suffix('€'),
                                        TextInput::make('users_count')
                                            ->label('Utilisateurs')
                                            ->numeric()
                                            ->minValue(0),
                                        TextInput::make('domain')
                                            ->label('Nom de domaine')
                                            ->maxLength(255)
                                            ->placeholder('lovatrip.fr'),
                                        DatePicker::make('domain_expires_at')
                                            ->label('Expiration du domaine')
                                            ->native(false)
                                            ->displayFormat('d/m/Y'),
                                        TagsInput::make('stack')
                                            ->label('Stack technique')
                                            ->placeholder('Laravel, Vue, OpenAI…')
                                            ->columnSpanFull(),
                                    ]),

                                Textarea::make('notes')
                                    ->label('Notes internes')
                                    ->rows(6)
                                    ->placeholder("Roadmap, idées, retours d'utilisateurs…"),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                // ---------- Colonne latérale ----------
                Group::make([
                    Section::make('Affichage')
                        ->schema([
                            Toggle::make('is_visible')
                                ->label('Visible sur le site')
                                ->helperText('Dans la section « Applications Lova ».'),

                            Toggle::make('is_featured')
                                ->label('Mise en avant')
                                ->helperText('Carte plus grande, en premier.'),

                            Select::make('status')
                                ->label('Statut')
                                ->options(LovaApp::STATUSES)
                                ->default('idea')
                                ->required()
                                ->native(false)
                                ->helperText('Sur le site : Idée / En dév. = « Bientôt ». Le bouton n\'apparaît qu\'en Bêta ou Disponible.'),

                            TextInput::make('sort_order')
                                ->label('Ordre')
                                ->numeric()
                                ->default(0)
                                ->helperText('Vous pouvez aussi glisser-déposer depuis la liste.'),
                        ]),

                    Section::make('Identité visuelle')
                        ->schema([
                            FileUpload::make('logo')
                                ->label('Icône')
                                ->image()
                                ->disk('uploads')
                                ->directory('apps')
                                ->visibility('public')
                                ->imageEditor()
                                ->imageAspectRatio('1:1')
                                ->automaticallyCropImagesToAspectRatio()
                                ->maxSize(2048)
                                ->helperText('Carré, 512×512 px conseillé. Vide = initiales sur la couleur.'),

                            ColorPicker::make('accent_color')
                                ->label("Couleur de l'app")
                                ->default('#4f46e5')
                                ->required(),

                            TextInput::make('category')
                                ->label('Catégorie')
                                ->maxLength(60)
                                ->datalist(fn() => LovaApp::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->all())
                                ->placeholder('Organisation, Voyage, Productivité…'),

                            Toggle::make('has_ai')
                                ->label('Propulsée par l\'IA')
                                ->helperText('Affiche un badge « IA ».'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }
}
