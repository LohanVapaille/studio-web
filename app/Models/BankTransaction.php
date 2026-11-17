<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    /**
     * Catégories Linxo considérées comme des mouvements entre mes propres comptes
     * (ni recette ni dépense) : exclues des totaux par défaut.
     */
    public const INTERNAL_CATEGORIES = [
        'Virements internes',
        'Achats, ventes de titres',
    ];

    protected $fillable = [
        'date', 'label', 'category', 'amount', 'notes', 'account', 'connection',
        'is_internal', 'is_professional', 'import_hash',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
            'is_internal' => 'boolean',
            'is_professional' => 'boolean',
        ];
    }

    public static function isInternalCategory(?string $category): bool
    {
        return in_array($category, self::INTERNAL_CATEGORIES, true);
    }

    /** Opérations réelles (hors virements internes). */
    public function scopeReal(Builder $query): Builder
    {
        return $query->where('is_internal', false);
    }
}
