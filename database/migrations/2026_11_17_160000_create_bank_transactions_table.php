<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('label');
            $table->string('category')->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->text('notes')->nullable();
            $table->string('account')->nullable();
            $table->string('connection')->nullable()->index(); // banque (« Nom de la connexion » chez Linxo)
            $table->boolean('is_internal')->default(false)->index(); // virement entre mes comptes : exclu des totaux
            $table->boolean('is_professional')->default(false)->index(); // recette / dépense liée à l'activité
            $table->string('import_hash', 40)->unique(); // anti-doublon entre deux imports
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
