@extends('layouts.admin')

@section('title', 'Nuovo livello preventivatore')

@section('content')
<div class="container-fluid py-4">

  {{-- Header --}}
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h4 class="mb-0">Nuovo livello</h4>
      <p class="text-sm text-secondary mb-0">
        Crea un livello logico del preventivatore (es: Tipo progetto, Funzionalità, Integrazioni).
      </p>
    </div>

    <a href="{{ route('admin.quote-levels.index') }}" class="btn btn-outline-dark">
      <i class="fa-solid fa-arrow-left me-2"></i> Indietro
    </a>
  </div>

  {{-- Errori --}}
  @if ($errors->any())
    <div class="alert alert-danger text-white">
      <strong>Ci sono errori nel form:</strong>
      <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Form --}}
  <form method="POST" action="{{ route('admin.quote-levels.store') }}">
    @csrf

    <div class="row">
      <div class="col-lg-8">

        <div class="card mb-4">
          <div class="card-header pb-0">
            <h6 class="mb-0">Dati livello</h6>
          </div>

          <div class="card-body">

            {{-- Titolo (FIX: era name) --}}
            <div class="mb-3">
              <label class="form-label">Nome livello</label>
              <input
                type="text"
                name="title"
                class="form-control"
                placeholder="Es: Tipo sito"
                value="{{ old('title') }}"
                required
              >
            </div>

            {{-- Step / Level (FIX: era sort_order ma il controller vuole level) --}}
            <div class="mb-3">
              <label class="form-label">Ordine (step)</label>
              <input
                type="number"
                name="level"
                class="form-control"
                min="1"
                max="20"
                value="{{ old('level', 1) }}"
                required
              >
              <small class="text-muted">
                Determina la sequenza dei livelli (1 = primo step).
              </small>
            </div>

            {{-- sort_order (opzionale, lo teniamo ma non obblighiamo) --}}
            <div class="mb-3">
              <label class="form-label">Sort interno (opzionale)</label>
              <input
                type="number"
                name="sort_order"
                class="form-control"
                min="0"
                max="9999"
                value="{{ old('sort_order', 0) }}"
              >
              <small class="text-muted">
                Serve solo per ordinamenti interni a parità di step.
              </small>
            </div>

            {{-- Tipo selezione (FIX: values single/multi) --}}
            <div class="mb-3">
              <label class="form-label">Tipo di selezione</label>
              <select name="selection_type" class="form-control" required>
                <option value="single" {{ old('selection_type', 'single') === 'single' ? 'selected' : '' }}>
                  Scelta unica (radio)
                </option>
                <option value="multi" {{ old('selection_type') === 'multi' ? 'selected' : '' }}>
                  Scelta multipla (checkbox)
                </option>
              </select>
            </div>

            {{-- Obbligatorio --}}
            <div class="form-check form-switch mb-3">
              <input
                class="form-check-input"
                type="checkbox"
                name="is_required"
                value="1"
                {{ old('is_required') ? 'checked' : '' }}
              >
              <label class="form-check-label">
                Livello obbligatorio
              </label>
            </div>

            {{-- Attivo --}}
            <div class="form-check form-switch">
              <input
                class="form-check-input"
                type="checkbox"
                name="is_active"
                value="1"
                {{ old('is_active', true) ? 'checked' : '' }}
              >
              <label class="form-check-label">
                Livello attivo
              </label>
            </div>

          </div>
        </div>

      </div>
    </div>

    {{-- Salva --}}
    <div class="card">
      <div class="card-body">
        <button type="submit" class="btn bg-gradient-dark w-100">
          <i class="fa-solid fa-floppy-disk me-2"></i> Salva livello
        </button>
      </div>
    </div>

  </form>

</div>
@endsection

