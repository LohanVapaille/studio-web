<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    public const SOURCES = [
        'google' => 'Google (logo affiché)',
        'direct' => 'Direct (sans logo)',
    ];

    protected $fillable = [
        'name',
        'role',
        'quote',
        'rating',
        'source',
        'photo',
        'reviewed_at',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'reviewed_at' => 'date',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    /** Avis visibles, dans l'ordre choisi dans le back-office. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order')->orderByDesc('id');
    }

    public function isFromGoogle(): bool
    {
        return $this->source === 'google';
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->photo
            ? Storage::disk('uploads')->url($this->photo)
            : asset('img/logo_lova.jpg'));
    }
}
