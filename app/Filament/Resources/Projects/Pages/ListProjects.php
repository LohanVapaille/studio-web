<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'tous' => Tab::make('Tous')
                ->badge(Project::count()),
            'web' => Tab::make('Web')
                ->icon('heroicon-o-code-bracket')
                ->badge(Project::where('type', 'web')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'web')),
            'graphisme' => Tab::make('Graphisme')
                ->icon('heroicon-o-paint-brush')
                ->badge(Project::where('type', 'graphic')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'graphic')),
            'lovaweb' => Tab::make('Sur Lovaweb')
                ->badge(Project::onLovaweb()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->onLovaweb()),
            'portfolio' => Tab::make('Sur le portfolio')
                ->badge(Project::onPortfolio()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->onPortfolio()),
        ];
    }
}
