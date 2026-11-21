<?php

namespace App\Models;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Application de l'écosystème Lova (LovaTrip, LovaTask, LovaCalendar…).
 */
class LovaApp extends Model
{
    public const STATUSES = [
        'idea' => 'Idée',
        'building' => 'En développement',
        'beta' => 'Bêta',
        'live' => 'Disponible',
        'paused' => 'En pause',
        'archived' => 'Archivée',
    ];

    /** Libellé affiché sur le site public (plus court, orienté visiteur). */
    public const PUBLIC_STATUSES = [
        'idea' => 'Bientôt',
        'building' => 'Bientôt',
        'beta' => 'Bêta',
        'live' => 'Disponible',
        'paused' => 'En pause',
        'archived' => 'Archivée',
    ];

    public const STATUS_COLORS = [
        'idea' => 'gray',
        'building' => 'info',
        'beta' => 'warning',
        'live' => 'success',
        'paused' => 'gray',
        'archived' => 'danger',
    ];

    public const PRICING_MODELS = [
        'free' => 'Gratuit',
        'freemium' => 'Freemium',
        'subscription' => 'Abonnement',
        'one_time' => 'Paiement unique',
    ];

    protected $fillable = [
        'name', 'slug', 'tagline', 'description', 'logo', 'accent_color', 'category', 'features', 'has_ai',
        'status', 'launched_at', 'url', 'admin_url', 'repo_url',
        'pricing_model', 'price_label', 'cta_label',
        'monthly_cost', 'cost_items', 'mrr', 'users_count', 'stack', 'domain', 'domain_expires_at', 'notes',
        'is_visible', 'is_featured', 'sort_order',
    ];

    protected $attributes = [
        'status' => 'idea',
        'pricing_model' => 'free',
        'accent_color' => '#4f46e5',
        'is_visible' => false,
        'is_featured' => false,
        'has_ai' => false,
        'sort_order' => 0,
    ];

    protected $casts = [
        'features' => 'array',
        'cost_items' => 'array',
        'stack' => 'array',
        'has_ai' => 'boolean',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'launched_at' => 'date',
        'domain_expires_at' => 'date',
        'monthly_cost' => 'decimal:2',
        'mrr' => 'decimal:2',
        'users_count' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (LovaApp $app) {
            if (blank($app->slug)) {
                $base = Str::slug($app->name) ?: 'app';
                $slug = $base;
                for ($i = 2; static::where('slug', $slug)->when($app->id, fn ($q) => $q->whereKeyNot($app->id))->exists(); $i++) {
                    $slug = "{$base}-{$i}";
                }
                $app->slug = $slug;
            }

            // Le coût mensuel suit le détail des coûts quand il est renseigné
            if (filled($app->cost_items)) {
                $app->monthly_cost = collect($app->cost_items)->sum(fn ($item) => (float) ($item['amount'] ?? 0));
            }
        });
    }

    /** Apps affichées sur le site : mises en avant d'abord, puis l'ordre choisi dans le back-office. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true)->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id');
    }

    public function isAvailable(): bool
    {
        return in_array($this->status, ['beta', 'live'], true) && filled($this->url);
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->logo ? Storage::disk('uploads')->url($this->logo) : null);
    }

    protected function publicStatus(): Attribute
    {
        return Attribute::get(fn () => self::PUBLIC_STATUSES[$this->status] ?? null);
    }

    /** Initiales affichées quand il n'y a pas de logo : « LovaTrip » → « Tr ». */
    protected function monogram(): Attribute
    {
        return Attribute::get(function () {
            $name = preg_replace('/^lova\s*/i', '', $this->name) ?: $this->name;

            return Str::ucfirst(Str::lower(mb_substr($name, 0, 2)));
        });
    }

    protected function descriptionHtml(): Attribute
    {
        return Attribute::get(fn () => filled($this->description)
            ? RichContentRenderer::make($this->description)->toHtml()
            : '');
    }

    /** Marge mensuelle : revenus - coûts. */
    protected function monthlyMargin(): Attribute
    {
        return Attribute::get(fn () => (float) $this->mrr - (float) $this->monthly_cost);
    }
}
