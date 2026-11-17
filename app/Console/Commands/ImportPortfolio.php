<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\Project;
use App\Models\Technology;
use App\Models\TimelineEntry;
use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * Fusionne l'ancienne base du portfolio (projetsweb, projetsgfx, caracteristiques, images, techno, parcours, messages)
 * dans la base Lovaweb. Lecture seule côté portfolio. Peut être relancée sans créer de doublons :
 * chaque projet importé garde une référence (legacy_ref) et les champs déjà remplis côté Lovaweb ne sont pas écrasés.
 */
class ImportPortfolio extends Command
{
    protected $signature = 'portfolio:import
        {--connection=portfolio : Connexion vers la base du portfolio (config/database.php)}
        {--dry-run : Simule l\'import sans rien enregistrer}';

    protected $description = 'Importe les projets, caractéristiques, images, technologies, parcours et messages du portfolio';

    private ConnectionInterface $source;

    /** @var array<string, int> */
    private array $stats = [];

    public function handle(): int
    {
        try {
            $this->source = DB::connection($this->option('connection'));
            $this->source->getPdo();
        } catch (Throwable $e) {
            $this->error('Connexion à la base du portfolio impossible : ' . $e->getMessage());
            $this->line('Renseignez PORTFOLIO_DB_DATABASE / PORTFOLIO_DB_USERNAME / PORTFOLIO_DB_PASSWORD dans le .env.');

            return self::FAILURE;
        }

        DB::beginTransaction();

        try {
            $webTech = $this->importTechnologies('techno', 'id_techno', 'nom_techno', 'dev');
            $gfxTech = $this->importTechnologies('techno_gfx', 'id_techno_gfx', 'nom_techno_gfx', 'design');

            $this->importWebProjects($webTech);
            $this->importGraphicProjects($gfxTech);
            $this->importTimeline();
            $this->importMessages();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->error('Import annulé : ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->option('dry-run') ? DB::rollBack() : DB::commit();

        $this->table(['Élément', 'Nombre'], collect($this->stats)->map(fn ($n, $k) => [$k, $n])->values()->all());
        $this->info($this->option('dry-run') ? 'Simulation terminée : rien n\'a été enregistré.' : 'Import terminé.');

        return self::SUCCESS;
    }

    // ---------------------------------------------------------------------
    // Technologies
    // ---------------------------------------------------------------------

    /** @return array<int, int> id portfolio => id Lovaweb */
    private function importTechnologies(string $table, string $idColumn, string $nameColumn, string $category): array
    {
        $map = [];

        foreach ($this->source->table($table)->get() as $row) {
            $name = trim($row->{$nameColumn});
            if ($name === '') {
                continue;
            }

            $tech = Technology::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
            if (! $tech) {
                $tech = Technology::create(['name' => $name, 'category' => $category]);
                $this->count('Technologies créées');
            }

            $map[(int) $row->{$idColumn}] = $tech->id;
        }

        return $map;
    }

    // ---------------------------------------------------------------------
    // Projets
    // ---------------------------------------------------------------------

    private function importWebProjects(array $techMap): void
    {
        foreach ($this->source->table('projetsweb')->orderBy('id_projet')->get() as $row) {
            $id = (int) $row->id_projet;

            $project = $this->upsertProject("portfolio-web:{$id}", [
                'type' => 'web',
                'title' => $row->nom,
                'category' => $row->categorie,
                'date_label' => $row->date,
                'url' => $row->lien,
                'main_image' => $row->main_img,
                'mockup_image' => $row->mockup,
                'presentation' => $row->presentation,
                'role' => $row->role,
                'duration' => $row->duree,
                'short_description' => $row->{'lil-description'},
                'status' => $row->etat,
                'context' => $row->interet,
            ], (bool) $row->selected);

            $this->importFeatures($project, $this->source->table('caracteristiques')->where('id_projet', $id)->orderBy('id')->get());
            $this->importImages($project, $this->source->table('imagesweb')->where('id_projetweb', $id)->orderBy('id_imagesweb')->pluck('lien'));
            $this->attachTechnologies($project, $this->source->table('fk_techno')->where('id_projet', $id)->pluck('id_techno'), $techMap);
        }
    }

    private function importGraphicProjects(array $techMap): void
    {
        foreach ($this->source->table('projetsgfx')->orderBy('id')->get() as $row) {
            $id = (int) $row->id;

            $project = $this->upsertProject("portfolio-gfx:{$id}", [
                'type' => 'graphic',
                'title' => $row->titre,
                'category' => $row->categorie,
                'tags' => collect([$row->cara1, $row->cara2])->map(fn ($t) => trim((string) $t))->filter()->values()->all(),
                'date_label' => $row->date,
                'main_image' => $row->minia,
                'mockup_image' => $row->image1,
                'presentation' => $row->explication,
                'role' => $row->role,
                'short_description' => $row->{'lil-text'},
                'status' => $row->etat,
                'context' => $row->interet,
            ], false);

            $this->importImages($project, $this->source->table('imagesgfx')->where('id_projetgfx', $id)->orderBy('id')->pluck('lien'));
            $this->attachTechnologies($project, $this->source->table('fk_techno_gfx')->where('id_projet_gfx', $id)->pluck('id_techno_gfx'), $techMap);
        }
    }

    /**
     * Retrouve le projet (référence d'import, puis titre, puis adresse du site) ou le crée.
     * Un projet déjà présent sur Lovaweb garde ses textes : on ne complète que les champs vides
     * (ou une image dont le fichier est introuvable).
     */
    private function upsertProject(string $ref, array $data, bool $featuredOnPortfolio): Project
    {
        $data = array_map(fn ($v) => is_string($v) ? $this->clean($v) : $v, $data);

        $project = Project::where('legacy_ref', $ref)->first()
            ?? $this->findByTitle($data['title'])
            ?? $this->findByUrl($data['url'] ?? null, $data['type']);

        if (! $project) {
            $project = new Project($data + [
                'show_on_lovaweb' => false,   // nouveau sur Lovaweb : masqué, à activer depuis le back-office
                'show_on_portfolio' => true,
                'is_featured_portfolio' => $featuredOnPortfolio,
                'legacy_ref' => $ref,
            ]);
            $project->save();
            $this->count($data['type'] === 'web' ? 'Projets web créés' : 'Projets graphiques créés');

            return $project;
        }

        foreach ($data as $field => $value) {
            if (blank($value)) {
                continue;
            }
            $current = $project->{$field};
            $brokenImage = in_array($field, ['main_image', 'mockup_image'], true)
                && filled($current) && ! is_file(public_path($current)) && is_file(public_path($value));

            if (blank($current) || $brokenImage) {
                $project->{$field} = $value;
            }
        }

        // Premier rattachement seulement : ensuite, ce sont les réglages du back-office qui font foi
        if (blank($project->legacy_ref)) {
            $project->legacy_ref = $ref;
            $project->show_on_portfolio = true;
            $project->is_featured_portfolio = $featuredOnPortfolio;
        }

        if ($project->isDirty()) {
            $project->save();
            $this->count('Projets existants complétés');
        } else {
            $this->count('Projets déjà à jour');
        }

        return $project;
    }

    private function findByTitle(string $title): ?Project
    {
        $wanted = $this->normalize($title);

        return Project::all()->first(fn (Project $p) => $this->normalize($p->title) === $wanted);
    }

    private function findByUrl(?string $url, string $type): ?Project
    {
        $host = $url ? parse_url($url, PHP_URL_HOST) : null;
        if (! $host) {
            return null;
        }

        return Project::where('type', $type)->whereNull('legacy_ref')->get()
            ->first(fn (Project $p) => $p->url && parse_url($p->url, PHP_URL_HOST) === $host);
    }

    // ---------------------------------------------------------------------
    // Caractéristiques, images, technologies d'un projet
    // ---------------------------------------------------------------------

    /** Importe les caractéristiques seulement si le projet n'en a pas encore (celles de Lovaweb sont déjà relues). */
    private function importFeatures(Project $project, $rows): void
    {
        if ($rows->isEmpty() || $project->features()->exists()) {
            return;
        }

        foreach ($rows->values() as $i => $row) {
            $project->features()->create([
                'title' => $this->clean($row->h3),
                'description' => $this->clean($row->explication),
                'icon_class' => $this->iconClass($row->balisei_cara),
                'sort_order' => $i + 1,
            ]);
            $this->count('Caractéristiques importées');
        }
    }

    private function importImages(Project $project, $paths): void
    {
        $existing = $project->images()->pluck('path')->all();
        $order = count($existing);

        foreach ($paths as $path) {
            $path = trim($path);
            if ($path === '' || in_array($path, $existing, true)) {
                continue;
            }
            $project->images()->create(['path' => $path, 'sort_order' => ++$order]);
            $existing[] = $path;
            $this->count('Images importées');
        }
    }

    private function attachTechnologies(Project $project, $portfolioIds, array $techMap): void
    {
        $ids = collect($portfolioIds)->map(fn ($id) => $techMap[(int) $id] ?? null)->filter()->unique()->values()->all();
        $attached = $project->technologies()->syncWithoutDetaching($ids)['attached'];

        if ($attached) {
            $this->count('Liens projet ↔ technologie ajoutés', count($attached));
        }
    }

    // ---------------------------------------------------------------------
    // Parcours et messages
    // ---------------------------------------------------------------------

    private function importTimeline(): void
    {
        foreach ($this->source->table('parcours')->get() as $row) {
            $title = $this->clean($row->titre);
            $date = Carbon::parse($row->date_tri)->toDateString();

            if (TimelineEntry::where('title', $title)->whereDate('sorted_at', $date)->exists()) {
                continue;
            }

            (new TimelineEntry)->fill([
                'title' => $title,
                'sorted_at' => $date,
                'period_label' => $this->clean($row->annee),
                'institution' => $this->clean($row->etablissement),
                'description' => $this->clean((string) $row->description) ?: null,
            ])->save();
            $this->count('Étapes de parcours importées');
        }
    }

    private function importMessages(): void
    {
        foreach ($this->source->table('messages')->get() as $row) {
            $date = Carbon::parse($row->date)->startOfDay();
            $email = trim($row->mail);

            $exists = Message::where('source', 'portfolio')->where('email', $email)
                ->whereDate('created_at', $date)->exists();
            if ($exists) {
                continue;
            }

            $message = new Message([
                'name' => $this->decode($row->nom),
                'email' => $email,
                'subject' => $this->decode($row->objet) ?: null,
                'message' => $this->decode($row->contenu),
                'source' => 'portfolio',
                'is_read' => true,
            ]);
            $message->created_at = $date;
            $message->updated_at = $date;
            $message->save();
            $this->count('Messages importés');
        }
    }

    // ---------------------------------------------------------------------
    // Outils
    // ---------------------------------------------------------------------

    /** « <i class='bx  bx-lock'  ></i> » → « bx bx-lock » */
    private function iconClass(?string $html): ?string
    {
        if (! $html || ! preg_match('/class\s*=\s*[\'"]([^\'"]+)[\'"]/', $html, $m)) {
            return null;
        }

        return preg_replace('/\s+/', ' ', trim($m[1]));
    }

    /** Supprime les espaces et retours à la ligne superflus (les <br> du portfolio sont conservés). */
    private function clean(string $value): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = preg_replace("/[ \t]*\n[ \t]*/", "\n", $value);
        $value = preg_replace("/\n{2,}/", "\n", $value);

        return trim($value);
    }

    private function decode(string $value): string
    {
        return trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function normalize(string $value): string
    {
        return Str::of($value)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', ' ')->trim()->value();
    }

    private function count(string $label, int $n = 1): void
    {
        $this->stats[$label] = ($this->stats[$label] ?? 0) + $n;
    }
}
