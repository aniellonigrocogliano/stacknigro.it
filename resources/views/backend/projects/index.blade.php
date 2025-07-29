@extends('backend.layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <h4>Aggiungi Progetto</h4>
            <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="title" class="form-label text-dark fw-bold">Titolo</label>
                    <input type="text" name="title" class="form-control border border-dark" required>
                </div>

                <div class="form-group mb-3">
                    <label for="description" class="form-label text-dark fw-bold">Descrizione</label>
                    <textarea name="description" class="form-control border border-dark" rows="3" required></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="image_1" class="form-label text-dark fw-bold">Immagine 1</label>
                    <input type="file" name="image_1" class="form-control border border-dark" accept="image/*" required>
                </div>
                <div class="form-group mb-3">
                    <label for="image_2" class="form-label text-dark fw-bold">Immagine 2</label>
                    <input type="file" name="image_2" class="form-control border border-dark" accept="image/*" required>
                </div>
                <div class="form-group mb-3">
                    <label for="image_3" class="form-label text-dark fw-bold">Immagine 3</label>
                    <input type="file" name="image_3" class="form-control border border-dark" accept="image/*" required>
                </div>
                <div class="form-group mb-3">
                    <label for="image_4" class="form-label text-dark fw-bold">Immagine 4</label>
                    <input type="file" name="image_4" class="form-control border border-dark" accept="image/*" required>
                </div>
                <div class="form-group mb-3">
                    <label for="image_5" class="form-label text-dark fw-bold">Immagine 5</label>
                    <input type="file" name="image_5" class="form-control border border-dark" accept="image/*" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Salva
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card my-4">
        <div class="card-header bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
            <h6 class="text-white ps-3">Elenco Progetti</h6>
        </div>
        <div class="card-body px-0 pb-2">
            <div class="table-responsive p-3">
                <table class="table table-hover align-items-center">
                    <thead>
                        <tr>
                            <th>Titolo</th>
                            <th>Data Pubblicazione</th>
                            <th>Immagini</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        <tr>
                            <td>{{ $project->title }}</td>
                            <td>{{ $project->created_at->format('d/m/Y') }}</td>
                            <td>
                                @foreach(range(1, 5) as $num)
                                    @if($project->{"image_$num"})
                                        <img src="{{ asset($project->{"image_$num"}) }}" alt="Immagine {{ $num }}" style="max-width: 80px; margin-right: 5px;">
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo progetto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($projects->isEmpty())
                            <tr><td colspan="4" class="text-center">Nessun progetto presente.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
