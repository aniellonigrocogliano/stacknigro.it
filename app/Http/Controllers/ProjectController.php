<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProjectController extends Controller
{
    // ==== CONFIG ====
    private int $maxKb = 5120;             // validazione Laravel (KB per immagine in input)
    private int $targetW = 1600;           // output 16:9
    private int $targetH = 900;

    private int $webpQualityStart = 82;    // qualità iniziale
    private int $webpQualityMin = 60;      // non scendere sotto (altrimenti diventa brutta)

    private int $targetFileKb = 500;       // ✅ target peso (KB) per immagine in output

    public function index()
    {
        $projects = Project::withCount('images')->latest()->get();
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $maxKb = $this->maxKb;
        $targetFileKb = $this->targetFileKb;

        return view('admin.projects.create', compact('maxKb', 'targetFileKb'));
    }

    public function store(Request $request)
    {
        $maxKb = $this->maxKb;

        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'excerpt'      => ['nullable', 'string'],
            'body'         => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'images.*'     => ['nullable', 'image', "max:$maxKb"],
        ]);

        $project = Project::create([
            'title'        => $data['title'],
            'slug'         => Str::slug($data['title']) . '-' . Str::lower(Str::random(6)),
            'excerpt'      => $data['excerpt'] ?? null,
            'body'         => $data['body'] ?? null,
            'is_published' => (bool)($data['is_published'] ?? true),
        ]);

        $this->storeImages($project, $request);

        return redirect()->route('admin.projects.index')->with('success', 'Progetto creato.');
    }

    public function edit(Project $project)
    {
        $maxKb = $this->maxKb;
        $targetFileKb = $this->targetFileKb;

        // ✅ cover first + ordine subito coerente
        $project->load([
            'images' => fn ($q) => $q->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id')
        ]);

        return view('admin.projects.edit', compact('project', 'maxKb', 'targetFileKb'));
    }

    public function update(Request $request, Project $project)
    {
        $maxKb = $this->maxKb;

        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'excerpt'      => ['nullable', 'string'],
            'body'         => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'images.*'     => ['nullable', 'image', "max:$maxKb"],
        ]);

        $project->update([
            'title'        => $data['title'],
            'excerpt'      => $data['excerpt'] ?? null,
            'body'         => $data['body'] ?? null,
            'is_published' => (bool)($data['is_published'] ?? false),
        ]);

        $this->storeImages($project, $request);

        return redirect()->route('admin.projects.edit', $project)->with('success', 'Salvato.');
    }

    public function destroy(Project $project)
    {
        $project->load('images');

        foreach ($project->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Progetto eliminato.');
    }

    public function deleteImage(ProjectImage $image)
    {
        $projectId = $image->project_id;
        $wasCover  = (int) $image->is_cover === 1;

        Storage::disk('public')->delete($image->path);
        $image->delete();

        // se era cover, assegna una nuova cover (prima per sort_order poi id)
        if ($wasCover) {
            $newCover = ProjectImage::where('project_id', $projectId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            if ($newCover) {
                ProjectImage::where('project_id', $projectId)->update(['is_cover' => 0]);
                $newCover->update(['is_cover' => 1]);
            }
        }

        return redirect()->route('admin.projects.edit', $projectId)->with('success', 'Immagine eliminata.');
    }

    public function setCover(ProjectImage $image)
    {
        $project = $image->project;

        $project->images()->update(['is_cover' => 0]);
        $image->update(['is_cover' => 1]);

        // ✅ reload immediato così vedi subito stellina/ordine cover-first
        return redirect()->route('admin.projects.edit', $project)->with('success', 'Cover aggiornata.');
    }

    // ✅ DRAG&DROP: salva sort_order
    public function sortImages(Request $request, Project $project)
    {
        $data = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $images = ProjectImage::where('project_id', $project->id)
            ->whereIn('id', $data['ids'])
            ->get()
            ->keyBy('id');

        $order = 1;
        foreach ($data['ids'] as $id) {
            if (!isset($images[$id])) continue;
            $images[$id]->update(['sort_order' => $order]);
            $order++;
        }

        return response()->json(['ok' => true]);
    }

    private function storeImages(Project $project, Request $request): void
    {
        if (!$request->hasFile('images')) return;

        $manager = new ImageManager(new Driver());

        // progressivo: max sort_order + 1
        $nextOrder = (int) ($project->images()->max('sort_order') ?? 0);
        $nextOrder = $nextOrder > 0 ? $nextOrder + 1 : 1;

        $hasCoverAlready = $project->images()->where('is_cover', 1)->exists();

        foreach ($request->file('images') as $file) {
            if (!$file || !$file->isValid()) continue;

            $date = now()->format('dmY_His'); // ✅ DDMMYYYY_HHMMSS
            $slugTitle = Str::slug($project->title);
            $filename = "{$slugTitle}-{$nextOrder}-{$date}.webp";
            $path = "projects/{$project->id}/{$filename}";

            // 1) leggi
            $img = $manager->read($file->getPathname());

            // 2) crop 16:9 centrato + resize 1600x900
            $img = $img->cover($this->targetW, $this->targetH);

            // 3) compressione per stare sotto target KB se possibile
            $targetBytes = $this->targetFileKb * 1024;
            $quality = $this->webpQualityStart;

            while (true) {
                $encoded = $img->toWebp($quality)->toString();

                if (strlen($encoded) <= $targetBytes) {
                    break;
                }

                $quality -= 5;
                if ($quality < $this->webpQualityMin) {
                    // stop (non peggioriamo più)
                    break;
                }
            }

            Storage::disk('public')->put($path, $encoded);

            $makeCover = !$hasCoverAlready;

            $project->images()->create([
                'path'       => $path,
                'sort_order' => $nextOrder,
                'is_cover'   => $makeCover ? 1 : 0,
            ]);

            if ($makeCover) $hasCoverAlready = true;

            $nextOrder++;
        }
    }
}
