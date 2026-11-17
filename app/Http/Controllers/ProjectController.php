<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    /** Liste des projets web visibles sur Lovaweb. */
    public function index()
    {
        $projects = Project::onLovaweb()->ordered()->with('technologies')->get();

        return view('index', compact('projects'));
    }

    /** Page de présentation d'un projet. */
    public function show(Project $project)
    {
        // Les projets graphiques ou masqués ne sont visibles que sur le portfolio
        abort_unless($project->type === 'web' && $project->show_on_lovaweb, 404);

        $project->load(['technologies', 'features', 'images']);

        $others = Project::onLovaweb()->ordered()
            ->whereKeyNot($project->getKey())
            ->get();

        // Projet suivant / précédent dans l'ordre d'affichage
        $all = Project::onLovaweb()->ordered()->pluck('slug');
        $index = $all->search($project->slug);
        $previous = $index > 0 ? Project::where('slug', $all[$index - 1])->first() : null;
        $next = $index < $all->count() - 1 ? Project::where('slug', $all[$index + 1])->first() : null;

        return view('projects.show', [
            'project' => $project,
            'related' => $others->take(3),
            'previous' => $previous,
            'next' => $next,
        ]);
    }
}
