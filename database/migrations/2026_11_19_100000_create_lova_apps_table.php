<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Applications Lova (LovaTrip, LovaTask, LovaCalendar…) : l'écosystème d'outils édité par Lovaweb.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('lova_apps', function (Blueprint $table) {
            $table->id();

            // Identité
            $table->string('name');                              // LovaTrip
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();               // « Organisez vos voyages avec l'IA »
            $table->text('description')->nullable();             // HTML (éditeur riche)
            $table->string('logo')->nullable();                  // disque « uploads »
            $table->string('accent_color', 9)->default('#4f46e5');
            $table->string('category')->nullable();              // Organisation, Voyage, Productivité…
            $table->json('features')->nullable();                // points forts affichés
            $table->boolean('has_ai')->default(false);

            // Cycle de vie
            $table->string('status', 20)->default('idea');       // idea | building | beta | live | paused | archived
            $table->date('launched_at')->nullable();

            // Liens
            $table->string('url')->nullable();                   // adresse publique de l'app
            $table->string('admin_url')->nullable();             // back-office de l'app
            $table->string('repo_url')->nullable();              // dépôt Git

            // Offre
            $table->string('pricing_model', 20)->default('free'); // free | freemium | subscription | one_time
            $table->string('price_label')->nullable();           // « 4,99 €/mois »
            $table->string('cta_label')->nullable();             // « Essayer gratuitement »

            // Pilotage (privé, jamais affiché sur le site)
            $table->decimal('monthly_cost', 10, 2)->nullable();  // hébergement, API IA, nom de domaine…
            $table->json('cost_items')->nullable();              // détail : [{label, amount}]
            $table->decimal('mrr', 10, 2)->nullable();           // revenu mensuel récurrent
            $table->unsignedInteger('users_count')->nullable();
            $table->json('stack')->nullable();                   // Laravel, Vue, OpenAI…
            $table->string('domain')->nullable();
            $table->date('domain_expires_at')->nullable();
            $table->text('notes')->nullable();                   // idées, roadmap, accès…

            // Affichage
            $table->boolean('is_visible')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_visible', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lova_apps');
    }
};
