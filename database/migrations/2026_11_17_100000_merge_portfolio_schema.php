<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Fusion des structures Lovaweb + portfolio (lohanvapaille.fr) :
 * une seule base pour les projets web ET graphiques, affichables sur l'un ou l'autre site.
 */
return new class extends Migration {
    public function up(): void
    {
        // Les tables avaient été créées en MyISAM (moteur par défaut du serveur), qui ignore les transactions :
        // un import interrompu laisserait la base à moitié modifiée. Passage en InnoDB.
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            $tables = collect(DB::select('SHOW TABLE STATUS'))
                ->filter(fn ($t) => strcasecmp((string) $t->Engine, 'MyISAM') === 0)
                ->pluck('Name');

            foreach ($tables as $table) {
                DB::statement("ALTER TABLE `{$table}` ENGINE=InnoDB");
            }
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');            // URL : /projets/{slug}
            $table->string('type', 20)->default('web')->after('slug');      // web | graphic
            $table->json('tags')->nullable()->after('category');            // ex-cara1/cara2 du portfolio
            $table->boolean('show_on_lovaweb')->default(true)->after('is_featured');
            $table->boolean('show_on_portfolio')->default(false)->after('show_on_lovaweb');
            $table->boolean('is_featured_portfolio')->default(false)->after('show_on_portfolio'); // ex-« selected »
            $table->unsignedInteger('sort_order')->default(0)->after('is_featured_portfolio');
            $table->string('legacy_ref', 50)->nullable()->after('sort_order'); // ex : portfolio-web:5
        });

        // Slugs des projets existants
        $used = [];
        foreach (DB::table('projects')->orderBy('id')->get(['id', 'title']) as $project) {
            $base = Str::slug($project->title) ?: 'projet';
            $slug = $base;
            for ($i = 2; in_array($slug, $used, true); $i++) {
                $slug = "{$base}-{$i}";
            }
            $used[] = $slug;
            DB::table('projects')->where('id', $project->id)->update(['slug' => $slug]);
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->unique('slug');
            $table->unique('legacy_ref');
            $table->index(['type', 'show_on_lovaweb']);
        });

        Schema::table('project_features', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('icon_class');
        });

        Schema::table('project_images', function (Blueprint $table) {
            $table->string('caption')->nullable()->after('path');
            $table->unsignedInteger('sort_order')->default(0)->after('caption');
        });

        Schema::table('technologies', function (Blueprint $table) {
            $table->string('category', 20)->default('dev')->after('name'); // dev | design
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('email');
            $table->string('source', 20)->default('lovaweb')->after('message'); // lovaweb | portfolio
        });

        // Parcours (formation / expérience) du portfolio
        Schema::create('timeline_entries', function (Blueprint $table) {
            $table->id();
            $table->string('period_label');              // « Septembre 2024 »
            $table->string('title');
            $table->string('institution')->nullable();   // établissement / entreprise
            $table->text('description')->nullable();
            $table->date('sorted_at');                   // date réelle, pour le tri
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Tables en MyISAM, sans clés étrangères : on retire les lignes orphelines
        // (ex. caractéristiques et images d'un ancien projet n° 1 supprimé).
        $projectIds = DB::table('projects')->pluck('id');
        DB::table('project_features')->whereNotIn('project_id', $projectIds)->delete();
        DB::table('project_images')->whereNotIn('project_id', $projectIds)->delete();
        DB::table('project_technology')->whereNotIn('project_id', $projectIds)->delete();
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline_entries');

        Schema::table('messages', fn (Blueprint $table) => $table->dropColumn(['subject', 'source']));
        Schema::table('technologies', fn (Blueprint $table) => $table->dropColumn('category'));
        Schema::table('project_images', fn (Blueprint $table) => $table->dropColumn(['caption', 'sort_order']));
        Schema::table('project_features', fn (Blueprint $table) => $table->dropColumn('sort_order'));

        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropUnique(['legacy_ref']);
            $table->dropIndex(['type', 'show_on_lovaweb']);
        });
        Schema::table('projects', fn (Blueprint $table) => $table->dropColumn([
            'slug', 'type', 'tags', 'show_on_lovaweb', 'show_on_portfolio',
            'is_featured_portfolio', 'sort_order', 'legacy_ref',
        ]));
    }
};
