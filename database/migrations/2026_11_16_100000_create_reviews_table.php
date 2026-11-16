<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('name');                            // auteur, ex : "Sandrine V"
            $table->string('role')->nullable();                // entreprise / projet, ex : "Bulle de Bonheur"
            $table->text('quote');                             // texte de l'avis
            $table->unsignedTinyInteger('rating')->default(5); // 1 à 5 étoiles
            $table->string('source', 20)->default('google');   // google | direct
            $table->string('photo')->nullable();               // chemin sur le disque « uploads »
            $table->date('reviewed_at')->nullable();           // date de l'avis
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });

        // Reprise des avis qui étaient écrits en dur dans la page d'accueil
        $now = now();
        $base = ['rating' => 5, 'source' => 'direct', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now];

        DB::table('reviews')->insert([
            $base + ['sort_order' => 1, 'name' => 'Oumar S', 'role' => 'HuntersTeam', 'quote' => "Le site internet convient parfaitement à ce que nous avions besoin. Nous pouvons gérer tout le contenu en autonomie grâce au back-office."],
            $base + ['sort_order' => 2, 'name' => 'Sandrine V', 'role' => 'Bulle de Bonheur', 'quote' => "Prestation rapide, site internet livré dans les temps. Lohan est toujours bienveillant et conseille au mieux pour que le projet soit réussi. Je recommande vivement !"],
            $base + ['sort_order' => 3, 'name' => 'Tedy F', 'role' => 'Coach CRPE Tedy Florette', 'quote' => "Je recommande vivement les services de ce webdesigner. Dès le début du projet, il a fait preuve d'une grande réactivité et d'une excellente compréhension de mes besoins. Le résultat est parfaitement conforme à mes attentes, tant sur le plan esthétique que fonctionnel. Au-delà de ses compétences techniques, j'ai particulièrement apprécié sa disponibilité, son écoute et sa capacité à être force de proposition..."],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
