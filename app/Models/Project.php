<?php

namespace App\Models;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Projet partagé entre Lovaweb (projets web) et le portfolio lohanvapaille.fr (web + graphisme).
 */
class Project extends Model
{
    use HasFactory;

    public const TYPES = [
        'web' => 'Développement web',
        'graphic' => 'Création graphique',
    ];

    protected $fillable = [
        'title',
        'slug',
        'type',
        'category',
        'tags',
        'date_label',
        'url',
        'main_image',
        'mockup_image',
        'presentation',
        'role',
        'duration',
        'short_description',
        'status',
        'context',
        'is_featured',
        'show_on_lovaweb',
        'show_on_portfolio',
        'is_featured_portfolio',
        'sort_order',
        'legacy_ref',
    ];

    protected $attributes = [
        'type' => 'web',
        'show_on_lovaweb' => true,
        'show_on_portfolio' => false,
        'is_featured' => false,
        'is_featured_portfolio' => false,
        'sort_order' => 0,
    ];

    protected $casts = [
        'tags' => 'array',
        'is_featured' => 'boolean',
        'show_on_lovaweb' => 'boolean',
        'show_on_portfolio' => 'boolean',
        'is_featured_portfolio' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            if (blank($project->slug)) {
                $project->slug = static::uniqueSlug($project->title, $project->id);
            }
        });

        // Les tables sont en MyISAM (pas de clés étrangères) : on nettoie à la main
        static::deleting(function (Project $project) {
            $project->images()->delete();
            $project->features()->delete();
            $project->technologies()->detach();
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'projet';
        $slug = $base;

        for ($i = 2; static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    // ---------- Relations ----------

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function features(): HasMany
    {
        return $this->hasMany(ProjectFeature::class)->orderBy('sort_order')->orderBy('id');
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class, 'project_technology')->orderBy('name');
    }

    // ---------- Requêtes ----------

    /** Projets affichés sur Lovaweb : uniquement les projets web. */
    public function scopeOnLovaweb(Builder $query): Builder
    {
        return $query->where('type', 'web')->where('show_on_lovaweb', true);
    }

    public function scopeOnPortfolio(Builder $query): Builder
    {
        return $query->where('show_on_portfolio', true);
    }

    /** Ordre choisi dans le back-office (glisser-déposer), puis les plus récents. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    // ---------- Images ----------

    /**
     * URL d'une image stockée en chemin relatif à public/ (« img/WEB/x.jpg »).
     * Si le fichier n'existe pas ici (images graphiques restées sur le portfolio),
     * on le sert depuis le portfolio.
     */
    public static function assetUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (! is_file(public_path($path)) && ($portfolio = config('services.portfolio.url'))) {
            return rtrim($portfolio, '/') . '/' . str_replace(' ', '%20', $path);
        }

        return asset($path);
    }

    public function getMainImageUrlAttribute(): ?string
    {
        return static::assetUrl($this->main_image);
    }

    public function getMockupImageUrlAttribute(): ?string
    {
        return static::assetUrl($this->mockup_image);
    }

    /**
     * Présentation : HTML de l'éditeur de mise en page du back-office (titres, listes, citations…),
     * nettoyé par le moteur de rendu Filament (scripts et attributs dangereux retirés).
     */
    public function getPresentationHtmlAttribute(): string
    {
        $html = trim((string) $this->presentation);

        return $html === '' ? '' : RichContentRenderer::make($html)->toHtml();
    }
}
