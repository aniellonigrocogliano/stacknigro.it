
@extends('layouts.admin')

@section('title', 'Preventivi')

@section('content')
<div class="py-4 container-fluid">

    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-0">Preventivi</h4>
            <p class="mb-0 text-sm text-secondary">Configura livelli, opzioni e regole del preventivatore.</p>
        </div>
    </div>

    {{-- FLASH --}}
    @if (session('success'))
        <div class="text-white alert alert-success" role="alert">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="text-white alert alert-danger" role="alert">
            <div class="mb-1 fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Errori</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <style>
      /* Evita che gli input 'is-filled' prendano il colore primary (rosa/rosso) */
      .input-group.input-group-outline.is-filled .form-control,
      .input-group.input-group-outline.focused .form-control {
        border-color: #d2d6da !important;
      }
      .input-group.input-group-outline.is-filled .form-label,
      .input-group.input-group-outline.focused .form-label {
        color: #344767 !important;
      }
    </style>

    {{-- NAV PILLS (come da tuo snippet) --}}
    <div class="mb-4 nav-wrapper position-relative end-0">
  <ul class="p-1 nav nav-pills nav-fill" role="tablist">

    <li class="nav-item">
      <a class="nav-link mb-0 px-0 py-1 {{ ($tab ?? 'levels') === 'levels' ? 'active' : '' }}"
         data-bs-toggle="tab"
         href="#tab-levels"
         role="tab"
         aria-selected="{{ ($tab ?? 'levels') === 'levels' ? 'true' : 'false' }}">
        <i class="align-middle fa-solid fa-layer-group me-1"></i>
        Livelli
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link mb-0 px-0 py-1 {{ ($tab ?? 'levels') === 'options' ? 'active' : '' }}"
         data-bs-toggle="tab"
         href="#tab-options"
         role="tab"
         aria-selected="{{ ($tab ?? 'levels') === 'options' ? 'true' : 'false' }}">
        <i class="align-middle fa-solid fa-list-check me-1"></i>
        Opzioni
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link mb-0 px-0 py-1 {{ ($tab ?? 'levels') === 'rules' ? 'active' : '' }}"
         data-bs-toggle="tab"
         href="#tab-rules"
         role="tab"
         aria-selected="{{ ($tab ?? 'levels') === 'rules' ? 'true' : 'false' }}">
        <i class="align-middle fa-solid fa-diagram-project me-1"></i>
        Regole
      </a>
    </li>

  </ul>
</div>
    {{-- =========================================================
    | TAB: LIVELLI
    ========================================================== --}}
    <div class="tab-content">
<div class="tab-pane fade {{ ($tab ?? 'levels') === 'levels' ? 'show active' : '' }}" id="tab-levels" role="tabpanel">
        <div class="card">
            <div class="pb-0 card-header">
                <h6 class="mb-0">Livelli (fissi: 10)</h6>
                <p class="mb-0 text-sm text-secondary">Qui NON serve JavaScript: modifichi e premi salva.</p>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0 align-items-center">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Livello</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nome</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Min</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Max</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Attivo</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Salva</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($levels as $lvl)
                                <tr>
                                    <td class="align-middle"><span class="text-sm fw-bold">L{{ $lvl->level }}</span></td>

                                    <td class="align-middle" style="min-width: 360px;">
                                        <form method="POST" action="{{ route('quotes.levels.update', $lvl) }}" class="gap-2 d-flex align-items-center">
                                            @csrf
                                            @method('PUT')

                                            <div class="my-0 input-group input-group-outline {{ (isset($lvl->name) && trim($lvl->name) !== '') ? 'is-filled' : '' }}" style="min-width: 320px;">
                                                <label class="form-label">Nome</label>
                                                <input type="text" name="name" class="form-control" value="{{ $lvl->name }}" required>
                                            </div>
                                    </td>

                                    <td class="text-center align-middle" style="min-width: 150px;">
                                            <select name="selection_type" class="form-select">
                                                <option value="single" {{ $lvl->selection_type === 'single' ? 'selected' : '' }}>Single</option>
                                                <option value="multi"  {{ $lvl->selection_type === 'multi' ? 'selected' : '' }}>Multi</option>
                                            </select>
                                    </td>

                                    <td class="text-center align-middle" style="min-width: 90px;">
                                            <input type="number" name="min_select" class="text-center form-control" min="0" max="10" value="{{ (int)$lvl->min_select }}">
                                    </td>

                                    <td class="text-center align-middle" style="min-width: 90px;">
                                            <input type="number" name="max_select" class="text-center form-control" min="0" max="10" value="{{ $lvl->max_select }}" placeholder="—">
                                    </td>

                                    <td class="text-center align-middle">
                                            <input type="hidden" name="is_active" value="0">
                                            <div class="m-0 form-check form-switch d-inline-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $lvl->is_active ? 'checked' : '' }}>
                                            </div>
                                    </td>

                                    <td class="text-center align-middle" style="min-width: 120px;">
                                            <button type="submit" class="mb-0 btn btn-sm btn-outline-success" title="Salva">
                                                <i class="fa-solid fa-floppy-disk"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
    | TAB: OPZIONI
    ========================================================== --}}
    <div class="tab-pane fade {{ ($tab ?? 'levels') === 'options' ? 'show active' : '' }}" id="tab-options" role="tabpanel">
        <div class="card">
            <div class="pb-0 card-header">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-12">
                        <h6 class="mb-0">Opzioni</h6>
                        <p class="mb-0 text-sm text-secondary">Seleziona un livello e gestisci le opzioni collegate.</p>
                    </div>
                    <div class="mt-3 col-lg-6 col-12 mt-lg-0">
                        <form method="GET" action="{{ route('quotes.index') }}" class="gap-2 d-flex justify-content-lg-end">
                            <input type="hidden" name="tab" value="options">
                            <select name="level_id" class="form-select" style="max-width: 320px;">
                                @foreach ($levels as $lvl)
                                    <option value="{{ $lvl->id }}" {{ (int)($selectedLevelId ?? 0) === (int)$lvl->id ? 'selected' : '' }}>
                                        L{{ $lvl->level }} — {{ $lvl->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="mb-0 btn btn-outline-success" type="submit">
                                <i class="fa-solid fa-check me-1"></i> Vai
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (empty($selectedLevel))
                    <div class="py-4 text-center text-secondary">Seleziona un livello.</div>
                @else
                    {{-- CREA OPZIONE --}}
                    <div class="p-3 mb-4 border rounded-3">
                        <h6 class="mb-1">Crea opzione</h6>
                        <p class="mb-0 text-sm text-secondary">Crea una nuova opzione e la collega a <strong>L{{ $selectedLevel->level }}</strong>.</p>

                        <form method="POST" action="{{ route('quotes.options.store') }}" class="mt-1 row g-3">
                            @csrf
                            <input type="hidden" name="level_id" value="{{ $selectedLevel->id }}">

                            <div class="col-lg-4">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Nome</label>
                                    <input class="form-control" type="text" name="name" required>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Descrizione</label>
                                    <input class="form-control" type="text" name="description">
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Ore</label>
                                    <input class="form-control" type="number" name="hours" min="0">
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Prezzo</label>
                                    <input class="form-control" type="number" step="0.01" name="price" min="0">
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="mt-2 form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                    <label class="form-check-label">Attiva</label>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="mt-2 form-check form-switch">
                                    <input type="hidden" name="is_default" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1">
                                    <label class="form-check-label">Default</label>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="mt-2 form-check form-switch">
                                    <input type="hidden" name="pivot_is_required" value="0">
                                    <input class="form-check-input" type="checkbox" name="pivot_is_required" value="1">
                                    <label class="form-check-label">Req</label>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="mt-2 form-check form-switch">
                                    <input type="hidden" name="pivot_is_hidden_by_default" value="0">
                                    <input class="form-check-input" type="checkbox" name="pivot_is_hidden_by_default" value="1">
                                    <label class="form-check-label">Hidden</label>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Ordine</label>
                                    <input class="form-control" type="number" name="pivot_sort_order" min="0" value="0">
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="mb-0 btn btn-outline-success" type="submit">
                                    <i class="fa-solid fa-plus me-1"></i> Crea
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- COLLEGA OPZIONE ESISTENTE --}}
                    <div class="p-3 mb-4 border rounded-3">
                        <h6 class="mb-1">Collega opzione esistente</h6>
                        <p class="mb-0 text-sm text-secondary">Collega un'opzione già presente a <strong>L{{ $selectedLevel->level }}</strong>.</p>

                        <form method="POST" action="{{ route('quotes.options.attach') }}" class="mt-1 row g-3">
                            @csrf
                            <input type="hidden" name="level_id" value="{{ $selectedLevel->id }}">

                            <div class="col-lg-6">
                                <select class="form-select" name="option_id" required>
                                    <option value="" disabled selected>Seleziona opzione…</option>
                                    @foreach ($availableOptions as $opt)
                                        <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <div class="mt-2 form-check form-switch">
                                    <input type="hidden" name="pivot_is_required" value="0">
                                    <input class="form-check-input" type="checkbox" name="pivot_is_required" value="1">
                                    <label class="form-check-label">Req</label>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="mt-2 form-check form-switch">
                                    <input type="hidden" name="pivot_is_hidden_by_default" value="0">
                                    <input class="form-check-input" type="checkbox" name="pivot_is_hidden_by_default" value="1">
                                    <label class="form-check-label">Hidden</label>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Ordine</label>
                                    <input class="form-control" type="number" name="pivot_sort_order" min="0" value="0">
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="mb-0 btn btn-outline-success" type="submit">
                                    <i class="fa-solid fa-link me-1"></i> Collega
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- LISTA OPZIONI --}}
                    <div class="table-responsive">
                        <table class="table mb-0 align-items-center">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opzione</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ore</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Prezzo</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ordine</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Req</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hidden</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azioni</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if ($selectedLevel->options->isEmpty())
                                    <tr>
                                        <td colspan="7" class="py-4 text-center text-secondary">Nessuna opzione per questo livello.</td>
                                    </tr>
                                @else
                                    @foreach ($selectedLevel->options as $opt)
                                        <tr>
                                            <td class="align-middle" style="min-width: 360px;">
                                                <form method="POST" action="{{ route('quotes.options.update', $opt) }}" class="gap-2 d-flex align-items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="redirect_level_id" value="{{ $selectedLevel->id }}">

                                                    <div class="my-0 input-group input-group-outline {{ (isset($opt->name) && trim($opt->name) !== '') ? 'is-filled' : '' }}" style="min-width: 260px;">
                                                        <label class="form-label">Nome</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $opt->name }}" required>
                                                    </div>

                                                    <div class="my-0 input-group input-group-outline {{ (isset($opt->description) && trim($opt->description) !== '') ? 'is-filled' : '' }}" style="min-width: 260px;">
                                                        <label class="form-label">Descrizione</label>
                                                        <input type="text" name="description" class="form-control" value="{{ $opt->description }}">
                                                    </div>

                                                    <input type="hidden" name="hours" value="{{ $opt->hours }}">
                                                    <input type="hidden" name="price" value="{{ $opt->price }}">
                                                    <input type="hidden" name="is_active" value="{{ $opt->is_active ? 1 : 0 }}">
                                                    <input type="hidden" name="is_default" value="{{ $opt->is_default ? 1 : 0 }}">

                                                    <button type="submit" class="mb-0 btn btn-sm btn-outline-success" title="Salva opzione">
                                                        <i class="fa-solid fa-floppy-disk"></i>
                                                    </button>
                                                </form>
                                            </td>

                                            <td class="align-middle" style="min-width: 130px;">
                                                <form method="POST" action="{{ route('quotes.options.update', $opt) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="redirect_level_id" value="{{ $selectedLevel->id }}">
                                                    <input type="hidden" name="name" value="{{ $opt->name }}">
                                                    <input type="hidden" name="description" value="{{ $opt->description }}">
                                                    <input type="hidden" name="price" value="{{ $opt->price }}">
                                                    <input type="hidden" name="is_active" value="{{ $opt->is_active ? 1 : 0 }}">
                                                    <input type="hidden" name="is_default" value="{{ $opt->is_default ? 1 : 0 }}">
                                                    <input type="number" name="hours" class="form-control" min="0" value="{{ $opt->hours }}">
                                                    <button type="submit" class="mt-2 mb-0 btn btn-sm btn-outline-success w-100">Salva</button>
                                                </form>
                                            </td>

                                            <td class="align-middle" style="min-width: 150px;">
                                                <form method="POST" action="{{ route('quotes.options.update', $opt) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="redirect_level_id" value="{{ $selectedLevel->id }}">
                                                    <input type="hidden" name="name" value="{{ $opt->name }}">
                                                    <input type="hidden" name="description" value="{{ $opt->description }}">
                                                    <input type="hidden" name="hours" value="{{ $opt->hours }}">
                                                    <input type="hidden" name="is_active" value="{{ $opt->is_active ? 1 : 0 }}">
                                                    <input type="hidden" name="is_default" value="{{ $opt->is_default ? 1 : 0 }}">
                                                    <input type="number" step="0.01" name="price" class="form-control" min="0" value="{{ $opt->price }}">
                                                    <button type="submit" class="mt-2 mb-0 btn btn-sm btn-outline-success w-100">Salva</button>
                                                </form>
                                            </td>

                                            <td class="text-center align-middle" style="min-width: 130px;">
                                                <form method="POST" action="{{ route('quotes.levels.options.update', [$selectedLevel, $opt]) }}" class="gap-2 d-flex justify-content-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="number" name="sort_order" class="text-center form-control" style="max-width: 90px;" min="0" value="{{ (int)($opt->pivot->sort_order ?? 0) }}">
                                                    <button type="submit" class="mb-0 btn btn-sm btn-outline-success" title="Salva">
                                                        <i class="fa-solid fa-floppy-disk"></i>
                                                    </button>
                                                </form>
                                            </td>

                                            <td class="text-center align-middle" style="min-width: 100px;">
                                                <form method="POST" action="{{ route('quotes.levels.options.update', [$selectedLevel, $opt]) }}" class="gap-2 d-flex justify-content-center align-items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="is_required" value="0">
                                                    <div class="m-0 form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_required" value="1" {{ !empty($opt->pivot?->is_required) ? 'checked' : '' }}>
                                                    </div>
                                                    <button type="submit" class="mb-0 btn btn-sm btn-outline-success" title="Salva">
                                                        <i class="fa-solid fa-floppy-disk"></i>
                                                    </button>
                                                </form>
                                            </td>

                                            <td class="text-center align-middle" style="min-width: 120px;">
                                                <form method="POST" action="{{ route('quotes.levels.options.update', [$selectedLevel, $opt]) }}" class="gap-2 d-flex justify-content-center align-items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="is_hidden_by_default" value="0">
                                                    <div class="m-0 form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_hidden_by_default" value="1" {{ !empty($opt->pivot?->is_hidden_by_default) ? 'checked' : '' }}>
                                                    </div>
                                                    <button type="submit" class="mb-0 btn btn-sm btn-outline-success" title="Salva">
                                                        <i class="fa-solid fa-floppy-disk"></i>
                                                    </button>
                                                </form>
                                            </td>

                                            <td class="text-center align-middle" style="min-width: 150px;">
                                                <form method="POST" action="{{ route('quotes.levels.options.detach', [$selectedLevel, $opt]) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="mb-0 btn btn-sm btn-outline-danger" type="submit" title="Scollega dal livello">
                                                        <i class="fa-solid fa-link-slash"></i>
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('quotes.options.destroy', $opt) }}" class="d-inline" onsubmit="return confirm('Eliminare definitivamente questa opzione?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="mb-0 btn btn-sm btn-outline-danger" type="submit" title="Elimina opzione">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>

                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- =========================================================
    | TAB: REGOLE
    ========================================================== --}}

  <div class="tab-pane fade {{ ($tab ?? 'levels') === 'rules' ? 'show active' : '' }}" id="tab-rules" role="tabpanel">
        <div class="card">
            <div class="pb-0 card-header">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-12">
                        <h6 class="mb-0">Regole</h6>
                        <p class="mb-0 text-sm text-secondary">Selezioni il <strong>livello target</strong> (quello che viene vincolato).</p>
                    </div>

                    <div class="mt-3 col-lg-6 col-12 mt-lg-0">
                        <form method="GET" action="{{ route('quotes.index') }}" class="gap-2 d-flex justify-content-lg-end">
                            <input type="hidden" name="tab" value="rules">
                            <select name="rules_level_id" class="form-select" style="max-width: 320px;">
                                @foreach ($levels as $lvl)
                                    <option value="{{ $lvl->id }}" {{ (int)($rulesLevelId ?? 0) === (int)$lvl->id ? 'selected' : '' }}>
                                        Target: L{{ $lvl->level }} — {{ $lvl->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="mb-0 btn btn-outline-success" type="submit">
                                <i class="fa-solid fa-check me-1"></i> Vai
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (empty($rulesTargetLevel))
                    <div class="py-4 text-center text-secondary">Seleziona un livello target.</div>
                @else
                    <div class="p-3 mb-4 border rounded-3">
                        <h6 class="mb-1">Crea regola per L{{ $rulesTargetLevel->level }}</h6>
                        <p class="mb-0 text-sm text-secondary">
                            Trigger = opzione di <strong>L{{ max(1, $rulesTargetLevel->level - 1) }}</strong>.
                            Azione = rende l'opzione target <strong>obbligatoria</strong> oppure <strong>nascosta</strong>.
                        </p>

                        @if ($rulesTargetLevel->level <= 1)
                            <div class="mt-3 text-sm text-warning">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                L1 non può dipendere da un livello precedente.
                            </div>
                        @elseif ($rulesTriggerOptions->isEmpty())
                            <div class="mt-3 text-sm text-warning">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                Nessuna opzione nel livello precedente: crea/collega opzioni prima.
                            </div>
                        @elseif ($rulesTargetOptions->isEmpty())
                            <div class="mt-3 text-sm text-warning">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                Nessuna opzione nel livello target: crea/collega opzioni prima.
                            </div>
                        @else
                            <form method="POST" action="{{ route('quotes.rules.store') }}" class="mt-1 row g-3">
                                @csrf
                                <input type="hidden" name="rules_level_id" value="{{ $rulesLevelId }}">
                                <input type="hidden" name="target_level_id" value="">

                                <div class="col-lg-4">
                                    <label class="mb-1 text-sm form-label">Se (trigger) opzione in L{{ $rulesTargetLevel->level - 1 }}</label>
                                    <select class="form-select" name="trigger_option_id" required>
                                        @foreach ($rulesTriggerOptions as $o)
                                            <option value="{{ $o->id }}">{{ $o->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-3">
                                    <label class="mb-1 text-sm form-label">Allora (azione)</label>
                                    <select class="form-select" name="action_type" required>
                                        <option value="require_option">Rendi obbligatoria</option>
                                        <option value="hide_option">Nascondi</option>
                                        <option value="show_option">Mostra</option>
                                    </select>
                                </div>

                                <div class="col-lg-4">
                                    <label class="mb-1 text-sm form-label">Opzione target in L{{ $rulesTargetLevel->level }}</label>
                                    <select class="form-select" name="target_option_id" required>
                                        @foreach ($rulesTargetOptions as $o)
                                            <option value="{{ $o->id }}">{{ $o->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-1">
                                    <label class="mb-1 text-sm form-label">Ord</label>
                                    <input class="form-control" type="number" name="sort_order" min="0" value="0">
                                </div>

                                <div class="col-12">
                                    <button class="mb-0 btn btn-outline-success" type="submit">
                                        <i class="fa-solid fa-plus me-1"></i> Crea regola
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table mb-0 align-items-center">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trigger</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azione</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Target</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ord</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($rules->isEmpty())
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-secondary">Nessuna regola per questo livello target.</td>
                                    </tr>
                                @else
                                    @foreach ($rules as $r)
                                        <tr>
                                            <td class="align-middle">
                                                <span class="text-sm">{{ $r->triggerOption?->name ?? ('#'.$r->trigger_option_id) }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge bg-gradient-info">{{ $r->action_type }}</span>
                                            </td>
                                            <td class="align-middle">
                                                @if ($r->targetOption)
                                                    <span class="text-sm">{{ $r->targetOption->name }}</span>
                                                @elseif ($r->targetLevel)
                                                    <span class="text-sm">L{{ $r->targetLevel->level }} — {{ $r->targetLevel->name }}</span>
                                                @else
                                                    <span class="text-sm text-secondary">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">{{ (int)$r->sort_order }}</td>
                                            <td class="text-center align-middle" style="min-width: 120px;">
                                                <form method="POST" action="{{ route('quotes.rules.destroy', $r) }}" class="d-inline" onsubmit="return confirm('Eliminare questa regola?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="mb-0 btn btn-sm btn-outline-danger" type="submit">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection
