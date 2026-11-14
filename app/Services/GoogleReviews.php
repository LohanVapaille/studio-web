<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Récupère la note et les avis de la fiche Google Business via Places API (New).
 * Le résultat est mis en cache pour limiter les appels (et rester dans la gratuité).
 */
class GoogleReviews
{
    private const CACHE_KEY = 'google_reviews';

    /**
     * @return array{rating: float, total: int, url: ?string, reviews: array<int, array>}|null
     *         null si l'API n'est pas configurée ou indisponible.
     */
    public function get(): ?array
    {
        $key = config('services.google_places.key');
        $placeId = config('services.google_places.place_id');

        if (! $key || ! $placeId) {
            return null;
        }

        $cached = Cache::get(self::CACHE_KEY);
        if ($cached !== null) {
            return $cached ?: null; // false = échec récent mis en cache
        }

        $data = $this->fetch($key, $placeId);

        // Succès : 24 h de cache. Échec : on réessaie dans 1 h, sans bloquer le site.
        Cache::put(
            self::CACHE_KEY,
            $data ?? false,
            $data ? now()->addHours((int) config('services.google_places.cache_hours', 24)) : now()->addHour()
        );

        return $data;
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function fetch(string $key, string $placeId): ?array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-Goog-Api-Key' => $key,
                    'X-Goog-FieldMask' => 'rating,userRatingCount,googleMapsUri,reviews',
                ])
                ->get('https://places.googleapis.com/v1/places/' . rawurlencode($placeId), [
                    'languageCode' => 'fr',
                ]);

            if (! $response->successful()) {
                Log::warning('Google Places : réponse ' . $response->status(), ['body' => $response->body()]);

                return null;
            }

            $json = $response->json();

            $reviews = collect($json['reviews'] ?? [])
                ->map(fn (array $r) => [
                    'name' => $r['authorAttribution']['displayName'] ?? 'Client Google',
                    'author_url' => $r['authorAttribution']['uri'] ?? null,
                    'photo' => $r['authorAttribution']['photoUri'] ?? null,
                    'rating' => (int) ($r['rating'] ?? 5),
                    // Texte original de l'auteur en priorité (Google interdit de le modifier)
                    'quote' => $r['originalText']['text'] ?? $r['text']['text'] ?? '',
                    'date' => $r['relativePublishTimeDescription'] ?? null,
                ])
                ->filter(fn (array $r) => $r['quote'] !== '')
                ->values()
                ->all();

            return [
                'rating' => (float) ($json['rating'] ?? 0),
                'total' => (int) ($json['userRatingCount'] ?? 0),
                'url' => $json['googleMapsUri'] ?? null,
                'reviews' => $reviews,
            ];
        } catch (Throwable $e) {
            Log::warning('Google Places : ' . $e->getMessage());

            return null;
        }
    }
}
