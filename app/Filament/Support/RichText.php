<?php

namespace App\Filament\Support;

use Filament\Forms\Components\RichEditor;

/**
 * Éditeur de mise en page partagé (projets, applications Lova).
 * Le contenu est enregistré en HTML : il s'affiche tel quel sur Lovaweb ET sur le portfolio,
 * qui lit la même base. Pas d'images dans le texte (la galerie du projet sert à ça) :
 * leur URL dépendrait du site qui les a envoyées.
 */
class RichText
{
    public static function make(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'link'],
                ['h2', 'h3', 'lead', 'small'],
                ['blockquote', 'bulletList', 'orderedList', 'horizontalRule'],
                ['table'],
                ['clearFormatting', 'undo', 'redo'],
            ])
            ->helperText('H2 pour les grandes parties, H3 pour les sous-parties. « Lead » = phrase d\'introduction mise en valeur, « Citation » = encadré.')
            ->extraInputAttributes(['style' => 'min-height: 22rem']);
    }
}
