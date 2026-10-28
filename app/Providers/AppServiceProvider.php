<?php

namespace App\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Évite l'erreur "1071 - La clé est trop longue" sur les anciennes
        // configurations MySQL / MariaDB avec utf8mb4 (limite d'index).
        Builder::defaultStringLength(191);
    }
}