@extends('layouts.admin')

@section('title', 'Nuova opzione')

@section('content')
<div class="py-4 container-fluid">

  <div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
      <h4 class="mb-0">Nuova opzione</h4>
      <p class="mb-0 text-sm text-secondary">
        Crea un’opzione riutilizzabile (ore/prezzi). Poi la assegni ai livelli.
      </p>
    </div>

    <a href="{{ route('admin.quote-options.index') }}" class="mb-0 btn btn-outline-dark">
      <i class="fa-solid fa-arrow-left me-2"></i> Indietro
    </a>
  </div>

  @if ($errors->any())
    <div class="text-white alert alert-danger">
      <strong>Ci sono errori nel form:</strong>
      <ul class="mt-2 mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.quote-options.store') }}">
    @csrf

    <div class="row">
      <div class="col-lg-8">

        <div class="mb-4 card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Dati opzione</h6>
          </div>

          <div class="card-body">

            <div class="mb-3">
              <label class="form-label">Label</label>
              <input type="text"
                     name="label"
                     class="form-control"
                     value="{{ old('label') }}"
                     required>
            </div>

            <div class="mb-3">
              <label class="form-label">Help text (opzionale)</label>
              <textarea name="help_text"
                        class="form-control"
                        rows="3"
                        placeholder="Testo di supporto/descrizione">{{ old('help_text') }}</textarea>
            </div>

            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-sm text-dark">Attiva</div>
                <div class="text-xs text-secondary">Se disattiva non compare nel frontend</div>
              </div>
              <div class="m-0 form-check form-switch">
                <input class="form-check-input"
                       type="checkbox"
                       role="switch"
                       name="is_active"
                       value="1"
                       {{ old('is_active', 1) ? 'checked' : '' }}>
              </div>
            </div>

          </div>
        </div>

        <div class="card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Range ore e prezzi</h6>
            <p class="mb-0 text-sm text-secondary">Min/Max per calcolo preventivo.</p>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <label class="form-label">Ore min</label>
                <input type="number"
                       name="hours_min"
                       class="form-control"
                       min="0"
                       value="{{ old('hours_min') }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">Ore max</label>
                <input type="number"
                       name="hours_max"
                       class="form-control"
                       min="0"
                       value="{{ old('hours_max') }}">
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-label">Prezzo min (€)</label>
                <input type="number"
                       name="price_min"
                       class="form-control"
                       min="0"
                       value="{{ old('price_min') }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">Prezzo max (€)</label>
                <input type="number"
                       name="price_max"
                       class="form-control"
                       min="0"
                       value="{{ old('price_max') }}">
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <button type="submit" class="btn bg-gradient-dark w-100 mb-0">
              <i class="fa-solid fa-floppy-disk me-2"></i> Salva opzione
            </button>
          </div>
        </div>
      </div>

    </div>
  </form>

</div>
@endsection
