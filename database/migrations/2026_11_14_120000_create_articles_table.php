<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();             // URL : /blog/{slug}
            $table->string('title');                      // titre H1
            $table->string('seo_title')->nullable();      // balise <title> (sinon : title | Lovaweb)
            $table->string('description', 320);          // meta description
            $table->string('excerpt', 500)->nullable();   // résumé des cartes (sinon : description)
            $table->string('category')->nullable();       // ex : "Budget", "SEO"
            $table->longText('content');                  // corps de l'article (HTML)
            $table->string('cover_image')->nullable();    // chemin sur le disque « uploads » (public/uploads), ex : blog/prix.jpg
            $table->unsignedSmallInteger('reading_time')->nullable(); // minutes (sinon calculé)
            $table->timestamp('published_at')->nullable(); // null = brouillon ; date future = programmé
            $table->timestamps();

            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
