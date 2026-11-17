<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Caractéristique / point fort d'un projet (ex-table « caracteristiques » du portfolio). */
class ProjectFeature extends Model
{
    protected $fillable = ['project_id', 'title', 'description', 'icon_class', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
