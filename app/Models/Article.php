<?php

namespace App\Models;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'seo_title',
        'description',
        'excerpt',
        'category',
        'content',
        'cover_image',
        'reading_time',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Slug généré automatiquement à partir du titre s'il n'est pas fourni
        static::saving(function (Article $article) {
            if (blank($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Articles visibles : publiés, et dont la date de publication est passée. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** Titre SEO : celui saisi, sinon « Titre | Lovaweb ». */
    protected function metaTitle(): Attribute
    {
        return Attribute::get(fn () => $this->seo_title ?: $this->title . ' | Lovaweb');
    }

    protected function summary(): Attribute
    {
        return Attribute::get(fn () => $this->excerpt ?: $this->description);
    }

    /** Temps de lecture : celui saisi, sinon ~200 mots/minute. */
    protected function readingMinutes(): Attribute
    {
        return Attribute::get(fn () => $this->reading_time
            ?: max(1, (int) ceil(count(preg_split('/\s+/u', trim(strip_tags($this->content)))) / 200)));
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->cover_image
            ? Storage::disk('uploads')->url($this->cover_image)
            : asset('img/allsites.png'));
    }

    /** Contenu HTML prêt à afficher : nettoyé, images du back-office résolues. */
    public function renderedContent(): string
    {
        return RichContentRenderer::make($this->content)
            ->fileAttachmentsDisk('uploads')
            ->fileAttachmentsVisibility('public')
            ->toHtml();
    }

    /** Jusqu'à $limit autres articles publiés, ceux de la même catégorie en premier. */
    public function related(int $limit = 3): Collection
    {
        return static::published()
            ->whereKeyNot($this->getKey())
            ->orderByRaw('category = ? desc', [$this->category])
            ->latest('published_at')
            ->take($limit)
            ->get();
    }
}
