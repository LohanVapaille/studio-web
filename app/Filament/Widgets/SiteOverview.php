<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\LovaApps\LovaAppResource;
use App\Filament\Resources\Messages\MessageResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Article;
use App\Models\LovaApp;
use App\Models\Message;
use App\Models\Project;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $unread = Message::where('is_read', false)->count();

        return [
            Stat::make('Messages non lus', $unread)
                ->description(Message::count() . ' au total')
                ->icon('heroicon-o-envelope')
                ->color($unread ? 'warning' : 'gray')
                ->url(MessageResource::getUrl('index')),

            Stat::make('Projets', Project::count())
                ->description(Project::onLovaweb()->count() . ' sur Lovaweb · ' . Project::onPortfolio()->count() . ' sur le portfolio')
                ->icon('heroicon-o-rectangle-stack')
                ->url(ProjectResource::getUrl('index')),

            Stat::make('Articles publiés', Article::published()->count())
                ->description(Article::whereNull('published_at')->count() . ' brouillon(s)')
                ->icon('heroicon-o-newspaper')
                ->url(ArticleResource::getUrl('index')),

            Stat::make('Avis clients', Review::where('is_published', true)->count())
                ->description('Note moyenne ' . number_format((float) Review::where('is_published', true)->avg('rating'), 1, ',', '') . '/5')
                ->icon('heroicon-o-star')
                ->url(ReviewResource::getUrl('index')),

            Stat::make('Applications Lova', LovaApp::whereIn('status', ['beta', 'live'])->count() . ' en ligne')
                ->description(LovaApp::count() . ' au total · coûts ' . number_format((float) LovaApp::sum('monthly_cost'), 2, ',', ' ') . ' €/mois · revenus ' . number_format((float) LovaApp::sum('mrr'), 2, ',', ' ') . ' €/mois')
                ->icon('heroicon-o-squares-2x2')
                ->url(LovaAppResource::getUrl('index')),
        ];
    }
}
