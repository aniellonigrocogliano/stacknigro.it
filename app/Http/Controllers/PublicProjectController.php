<?php

namespace App\Http\Controllers;

use App\Models\Project;

class PublicProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->where('is_published', 1)
            ->with(['images' => function ($q) {
                $q->orderByDesc('is_cover')
                  ->orderBy('sort_order')
                  ->orderBy('id');
            }])
            ->orderByDesc('id') // più recente prima
            ->paginate(12);

        return view('public.projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        abort_unless((int)$project->is_published === 1, 404);

        $project->load(['images' => function ($q) {
            $q->orderByDesc('is_cover')
              ->orderBy('sort_order')
              ->orderBy('id');
        }]);

        return view('public.projects.show', compact('project'));
    }
}
