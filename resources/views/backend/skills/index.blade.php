@extends('backend.layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <h4>Aggiungi Skill</h4>
            <form action="{{ route('admin.skills.store') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="name" class="form-label text-dark fw-bold">Nome</label>
                    <input type="text" name="name" class="form-control border border-dark" required>
                </div>

                <div class="form-group mb-3">
                    <label for="color" class="form-label text-dark fw-bold">Colore</label>
                    <div class="d-flex align-items-center">
                        <input type="color" id="colorPicker" class="form-control form-control-color me-3" value="#ff0000" title="Scegli colore">
                        <input type="text" name="color" id="colorHex" class="form-control border border-dark w-50" value="#ff0000" required>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="icon" class="form-label text-dark fw-bold">Codice FontAwesome</label>
                    <input type="text" name="icon" class="form-control border border-dark" placeholder="es: fa-brands fa-laravel" required>
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
            <h6 class="text-white ps-3">Elenco Skills</h6>
        </div>
        <div class="card-body px-0 pb-2">
            <div class="table-responsive p-3">
                <table class="table table-hover align-items-center">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Colore</th>
                            <th>Icona</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($skills as $skill)
                        <tr>
                            <td>{{ $skill->name }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $skill->color }}">
                                    {{ $skill->color }}
                                </span>
                            </td>
                            <td><i class="{{ $skill->icon }}"></i></td>
                            <td>
                                <form action="{{ route('admin.skills.destroy', $skill->id) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questa skill?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($skills->isEmpty())
                            <tr><td colspan="4" class="text-center">Nessuna skill presente.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const picker = document.getElementById('colorPicker');
    const hexInput = document.getElementById('colorHex');

    picker.addEventListener('input', () => {
        hexInput.value = picker.value;
    });

    hexInput.addEventListener('input', () => {
        if(/^#([0-9A-F]{3}){1,2}$/i.test(hexInput.value)) {
            picker.value = hexInput.value;
        }
    });
</script>
@endpush
