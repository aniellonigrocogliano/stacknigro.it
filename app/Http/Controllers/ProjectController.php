<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();
        return view('backend.projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_5' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $project = new Project();
        $project->title = $request->title;
        $project->description = $request->description;

        $uploadPath = public_path('uploads/projects');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        foreach (range(1, 5) as $i) {
            $input = "image_$i";
            if ($request->hasFile($input)) {
                $file = $request->file($input);
                $filename = "project_{$i}_" . time() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $project->$input = 'uploads/projects/' . $filename;
            }
        }

        $project->save();

        return redirect()->back()->with('success', 'Progetto salvato con successo!');
    }

    public function destroy(Project $project)
    {
        foreach (range(1, 5) as $i) {
            $field = "image_$i";
            if ($project->$field && file_exists(public_path($project->$field))) {
                unlink(public_path($project->$field));
            }
        }

        $project->delete();

        return redirect()->back()->with('success', 'Progetto eliminato!');
    }
}