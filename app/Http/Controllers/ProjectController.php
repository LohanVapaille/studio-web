<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->get();

        return view('projects.index', compact('projects'));

    }
}