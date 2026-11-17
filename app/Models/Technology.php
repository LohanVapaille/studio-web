<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Technology extends Model
{
    public const CATEGORIES = [
        'dev' => 'Développement',
        'design' => 'Design / graphisme',
    ];

    protected $fillable = ['name', 'category'];

    protected $attributes = ['category' => 'dev'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_technology');
    }
}
