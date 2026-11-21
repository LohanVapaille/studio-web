<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetAdminLocale;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            // Menu de l'avatar : retour au site public
            ->userMenuItems([
                Action::make('back-to-site')
                    ->label('Retour au site')
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->url(fn (): string => route('home'))
                    ->sort(-1),
            ])
            ->navigationGroups(['Contact', 'Portfolio', 'Lova', 'Contenu', 'Comptabilité'])
            // Icônes Boxicons des caractéristiques de projet (aperçu dans le back-office)
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => '<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">')
            // Thème clair glassmorphism (public/css/admin-glass.css)
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => '<link rel="stylesheet" href="' . asset('css/admin-glass.css') . '?v=' . @filemtime(public_path('css/admin-glass.css')) . '">')
            ->darkMode(false)
            ->sidebarWidth('16rem')
            ->brandName('Lovaweb')
            ->favicon(asset('img/logo_lova.jpg'))
            ->colors([
                'primary' => self::accessibleCyan(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                SetAdminLocale::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Cyan décalé d'un cran vers le foncé : les boutons (primary-600) passent en cyan-700,
     * ce qui donne un contraste AA (≥ 4.5:1) pour le texte blanc et pour les liens sur fond clair.
     */
    private static function accessibleCyan(): array
    {
        $c = Color::Cyan;

        return [
            50 => $c[50],
            100 => $c[100],
            200 => $c[200],
            300 => $c[300],
            400 => $c[500],
            500 => $c[600],
            600 => $c[700],
            700 => $c[800],
            800 => $c[900],
            900 => $c[950],
            950 => $c[950],
        ];
    }
}
