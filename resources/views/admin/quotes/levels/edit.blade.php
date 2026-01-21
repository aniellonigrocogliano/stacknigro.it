@extends('layouts.admin')

@section('title', 'Modifica livello')

@section('content')
<div class="container-fluid py-4">

  <div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
      <h4 class="mb-0">Modifica livello</h4>
      <p class="mb-0 text-sm text-secondary">Aggiorna le impostazioni del livello del preventivatore.</p>
    </div>

    <a href="{{ route('admin.quote-levels.index') }}" class="btn btn-outline-dark mb-0">
      <i class="fa-solid fa-arrow-left me-2"></i> Indietro
    </a>
  </div>

  @if (session('success'))
    <div class="alert alert-success text-white" role="alert">
      {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger text-white" role="alert">
      <strong>Ci sono errori nel form:</strong>
      <ul class="mt-2 mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row">
    <div class="col-lg-8">

      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6 class="mb-0">Dati livello</h6>
        </div>

        <div class="card-body">
          <form method="POST" action="{{ route('admin.quote-levels.update', $quoteLevel) }}" id="levelEditForm">
            @csrf
            @method('PUT')

            {{-- Step (level) --}}
            <div class="mb-3">
              <label class="form-label">Step (livello)</label>
              <input
                type="number"
                name="level"
                class="form-control"
                min="1"
                max="20"
                value="{{ old('level', $quoteLevel->level) }}"
                required
              >
            </div>

            {{-- Titolo --}}
            <div class="mb-3">
              <label class="form-label">Nome livello</label>
              <input
                type="text"
                name="title"
                class="form-control"
                value="{{ old('title', $quoteLevel->title) }}"
                required
              >
              <small class="text-xs text-secondary d-block mt-1">
                Esempio: “Tipo di sito”, “Funzioni”, “Extra”, ecc.
              </small>
            </div>

            {{-- Sort order --}}
            <div class="mb-3">
              <label class="form-label">Ordine (sort_order)</label>
              <input
                type="number"
                name="sort_order"
                class="form-control"
                min="0"
                max="9999"
                value="{{ old('sort_order', $quoteLevel->sort_order) }}"
              >
            </div>

            {{-- Tipo selezione --}}
            <div class="mb-3">
              <label class="form-label">Tipo selezione</label>
              <select name="selection_type" class="form-select" required>
                <option value="single" {{ old('selection_type', $quoteLevel->selection_type) === 'single' ? 'selected' : '' }}>
                  Scelta unica (single)
                </option>
                <option value="multi" {{ old('selection_type', $quoteLevel->selection_type) === 'multi' ? 'selected' : '' }}>
                  Scelte multiple (multi)
                </option>
              </select>

              <small class="text-xs text-secondary d-block mt-1">
                Il livello 1 sarà tipicamente “single”. Dal livello 2/3 spesso “multi”.
              </small>
            </div>

            {{-- Switch obbligatorio --}}
            <div class="mb-3 d-flex align-items-center justify-content-between">
              <div>
                <label class="form-label mb-0">Obbligatorio</label>
                <div class="text-xs text-secondary">L’utente deve selezionare almeno un’opzione in questo livello</div>
              </div>
              <div class="form-check form-switch m-0">
                <input
                  class="form-check-input"
                  type="checkbox"
                  role="switch"
                  name="is_required"
                  value="1"
                  {{ old('is_required', (int)$quoteLevel->is_required) ? 'checked' : '' }}
                >
              </div>
            </div>

            {{-- Switch attivo --}}
            <div class="mb-4 d-flex align-items-center justify-content-between">
              <div>
                <label class="form-label mb-0">Attivo</label>
                <div class="text-xs text-secondary">Se disattivo, il livello non appare nel preventivatore</div>
              </div>
              <div class="form-check form-switch m-0">
                <input
                  class="form-check-input"
                  type="checkbox"
                  role="switch"
                  name="is_active"
                  value="1"
                  {{ old('is_active', (int)$quoteLevel->is_active) ? 'checked' : '' }}
                >
              </div>
            </div>

            {{-- Salva --}}
            <div class="d-flex gap-2">
              <button type="submit" class="btn bg-gradient-dark mb-0">
                <i class="fa-solid fa-floppy-disk me-2"></i> Salva modifiche
              </button>

              <button
                type="button"
                class="btn btn-outline-danger mb-0 ms-auto"
                data-bs-toggle="modal"
                data-bs-target="#confirmDeleteModal"
                data-action="{{ route('admin.quote-levels.destroy', $quoteLevel) }}"
                data-title="Eliminare livello?"
                data-body="Vuoi eliminare il livello '{{ $quoteLevel->title }}'? Questa azione non è reversibile."
              >
                <i class="fa-solid fa-trash me-2"></i> Elimina livello
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>

    {{-- Colonna destra (info) --}}
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header pb-0">
          <h6 class="mb-0">Info</h6>
        </div>
        <div class="card-body">
          <div class="text-sm">
            <div class="mb-2">
              <span class="text-secondary">ID:</span>
              <strong class="ms-1">{{ $quoteLevel->id }}</strong>
            </div>
            <div class="mb-2">
              <span class="text-secondary">Creato:</span>
              <strong class="ms-1">{{ $quoteLevel->created_at?->format('d/m/Y H:i') }}</strong>
            </div>
            <div>
              <span class="text-secondary">Ultimo update:</span>
              <strong class="ms-1">{{ $quoteLevel->updated_at?->format('d/m/Y H:i') }}</strong>
            </div>
          </div>

          <hr class="horizontal dark my-3">

          <p class="text-xs text-secondary mb-0">
            Prossimo step: assegna le opzioni a questo livello (tab opzioni/pivot).
          </p>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- MODAL delete --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-normal" id="confirmDeleteTitle">Conferma eliminazione</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="confirmDeleteBody">Sei sicuro?</div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Annulla</button>

        <form method="POST" id="confirmDeleteForm">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn bg-gradient-danger">Elimina</button>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('confirmDeleteModal');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', (event) => {
      const btn = event.relatedTarget;
      if (!btn) return;

      document.getElementById('confirmDeleteForm').action = btn.getAttribute('data-action');
      document.getElementById('confirmDeleteTitle').innerText = btn.getAttribute('data-title') || 'Conferma eliminazione';
      document.getElementById('confirmDeleteBody').innerText = btn.getAttribute('data-body') || 'Sei sicuro?';
    });
  });
</script>
@endpush
