<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Models\Review;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // ---------- Colonne principale ----------
                Group::make([
                    Section::make('Avis')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nom du client')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Sandrine V'),

                            TextInput::make('role')
                                ->label('Entreprise / projet')
                                ->maxLength(255)
                                ->placeholder('Bulle de Bonheur')
                                ->helperText('Affiché sous le nom. Vide = « Avis Google » pour un avis Google.'),

                            Textarea::make('quote')
                                ->label("Texte de l'avis")
                                ->required()
                                ->rows(7)
                                ->maxLength(3000)
                                ->helperText('Collez le texte tel quel depuis Google, sans le modifier.'),

                            ToggleButtons::make('rating')
                                ->label('Note')
                                ->options([1 => '1 ★', 2 => '2 ★', 3 => '3 ★', 4 => '4 ★', 5 => '5 ★'])
                                ->default(5)
                                ->required()
                                ->inline(),
                        ]),
                ])->columnSpan(['lg' => 2]),

                // ---------- Colonne latérale ----------
                Group::make([
                    Section::make('Affichage')
                        ->schema([
                            Select::make('source')
                                ->label('Provenance')
                                ->options(Review::SOURCES)
                                ->default('google')
                                ->required()
                                ->native(false)
                                ->helperText('« Google » affiche le logo Google sur la carte.'),

                            DatePicker::make('reviewed_at')
                                ->label("Date de l'avis")
                                ->native(false)
                                ->displayFormat('d/m/Y'),

                            Toggle::make('is_published')
                                ->label('Visible sur le site')
                                ->default(true),

                            TextInput::make('sort_order')
                                ->label('Ordre')
                                ->numeric()
                                ->default(0)
                                ->helperText('Plus petit = affiché en premier. Vous pouvez aussi réordonner depuis la liste.'),

                            FileUpload::make('photo')
                                ->label('Photo du client')
                                ->image()
                                ->avatar()
                                ->disk('uploads')
                                ->directory('avis')
                                ->visibility('public')
                                ->imageEditor()
                                ->maxSize(2048)
                                ->helperText('Optionnel. Vide = logo Lovaweb.'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }
}
