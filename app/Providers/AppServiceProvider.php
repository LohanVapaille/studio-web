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

        // Les envois de fichiers transitent par un dossier temporaire Livewire, placé par défaut
        // sur le disque par défaut. On l'impose sur le disque privé (storage/app/private, non
        // servi publiquement) pour qu'un export bancaire ne passe jamais par un dossier web,
        // même si FILESYSTEM_DISK est mal réglé en prod.
        config(['livewire.temporary_file_upload.disk' => 'local']);
    }
}