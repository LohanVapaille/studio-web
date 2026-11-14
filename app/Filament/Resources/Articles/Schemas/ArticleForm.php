<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // ---------- Colonne principale ----------
                Group::make([
                    Section::make('Article')
                        ->schema([
                            TextInput::make('title')
                                ->label('Titre')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                // Remplit le slug tant qu'on ne l'a pas modifié à la main
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                    if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                                        $set('slug', Str::slug((string) $state));
                                    }
                                }),

                            TextInput::make('slug')
                                ->label('Adresse de la page')
                                ->prefix(url('/blog') . '/')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->rule('alpha_dash')
                                ->helperText("Minuscules et tirets. Évitez de la changer une fois l'article publié : les liens existants ne marcheraient plus."),

                            Textarea::make('excerpt')
                                ->label('Résumé')
                                ->rows(2)
                                ->maxLength(500)
                                ->helperText('Affiché sur les cartes de la page Blog. Vide = la meta description est utilisée.'),

                            RichEditor::make('content')
                                ->label('Contenu')
                                ->required()
                                ->toolbarButtons([
                                    ['bold', 'italic', 'underline', 'strike', 'link'],
                                    ['h2', 'h3', 'lead', 'small'],
                                    ['blockquote', 'bulletList', 'orderedList', 'horizontalRule'],
                                    ['table', 'attachFiles'],
                                    ['clearFormatting', 'undo', 'redo'],
                                ])
                                ->fileAttachmentsDisk('uploads')
                                ->fileAttachmentsDirectory('blog/contenu')
                                ->fileAttachmentsVisibility('public')
                                ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                                ->fileAttachmentsMaxSize(4096)
                                ->helperText('Titres : H2 pour les grandes parties, H3 pour les sous-parties (le H1 est le titre). « Lead » = chapeau d\'introduction, « Citation » = encadré coloré.')
                                ->extraInputAttributes(['style' => 'min-height: 28rem']),
                        ]),
                ])->columnSpan(['lg' => 2]),

                // ---------- Colonne latérale ----------
                Group::make([
                    Section::make('Publication')
                        ->schema([
                            DateTimePicker::make('published_at')
                                ->label('Date de publication')
                                ->seconds(false)
                                ->native(false)
                                ->displayFormat('d/m/Y H:i')
                                ->helperText('Vide = brouillon. Date future = publication programmée.'),

                            TextInput::make('category')
                                ->label('Catégorie')
                                ->maxLength(255)
                                ->datalist(fn () => Article::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->all())
                                ->placeholder('Budget, SEO, Conseils…'),

                            TextInput::make('reading_time')
                                ->label('Temps de lecture')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(120)
                                ->suffix('min')
                                ->helperText('Vide = calculé automatiquement.'),
                        ]),

                    Section::make('Référencement Google')
                        ->schema([
                            TextInput::make('seo_title')
                                ->label('Titre SEO')
                                ->maxLength(255)
                                ->live(debounce: 500)
                                ->placeholder(fn (Get $get) => filled($get('title')) ? $get('title') . ' | Lovaweb' : null)
                                ->helperText(fn (?string $state) => self::counter($state, 60, 'Vide = « Titre | Lovaweb ».')),

                            Textarea::make('description')
                                ->label('Meta description')
                                ->required()
                                ->rows(4)
                                ->maxLength(320)
                                ->live(debounce: 500)
                                ->helperText(fn (?string $state) => self::counter($state, 155, 'Le texte sous le titre dans Google.')),

                            FileUpload::make('cover_image')
                                ->label('Image de partage')
                                ->image()
                                ->disk('uploads')
                                ->directory('blog')
                                ->visibility('public')
                                ->imageEditor()
                                ->maxSize(4096)
                                ->helperText('Aperçu sur Facebook, LinkedIn, WhatsApp… Idéal : 1200 × 630 px. Vide = image par défaut du site.'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    /** « 48 / 60 caractères conseillés » + aide. */
    private static function counter(?string $state, int $ideal, string $help): string
    {
        $length = mb_strlen((string) $state);
        $warning = $length > $ideal ? ' — un peu long, Google risque de couper.' : '';

        return "{$length} / {$ideal} caractères conseillés{$warning} {$help}";
    }
}
