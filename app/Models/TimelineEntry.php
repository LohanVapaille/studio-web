<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Étape du parcours (formation, expérience) — ex-table « parcours » du portfolio. */
class TimelineEntry extends Model
{
    protected $fillable = ['period_label', 'title', 'institution', 'description', 'sorted_at', 'is_published'];

    protected $casts = [
        'sorted_at' => 'date',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderByDesc('sorted_at');
    }
}
