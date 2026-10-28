<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('date_label')->nullable(); // ex: "Avril 2025"
            $table->string('url')->nullable();
            $table->string('main_image')->nullable();
            $table->string('mockup_image')->nullable();
            $table->longText('presentation')->nullable(); // contenu détaillé (HTML)
            $table->string('role')->nullable();
            $table->string('duration')->nullable();
            $table->string('short_description', 500)->nullable(); // utilisé pour les cartes
            $table->string('status')->nullable(); // ex: "Actif / Visitable"
            $table->string('context')->nullable(); // ex: "Projet professionnel"
            $table->boolean('is_featured')->default(false); // anciennement "selected"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};