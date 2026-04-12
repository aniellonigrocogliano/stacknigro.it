@extends('layouts.admin')

@section('title', 'Preventivi')

@section('content')
<div class="py-4 container-fluid">

    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-0">Preventivi</h4>
            <p class="mb-0 text-sm text-secondary">Configura livelli, opzioni, regole e pacchetti del preventivatore.</p>
        </div>
    </div>

    {{-- FLASH E ERRORI --}}
    @if (session('success'))
        <div class="text-white alert alert-success" role="alert">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="text-white alert alert-danger" role="alert">
            <div class="mb-1 fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Si è verificato un errore</div>
            <ul class="mb-0 text-sm ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <style>
      .table-compact th, .table-compact td { padding: 0.5rem; vertical-align: middle; }
      .form-control-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; border-radius: 0.2rem; }
      .toggle-group label { margin-bottom: 0; cursor: pointer; user-select: none; }
      .text-xxs { font-size: 0.65rem !important; font-weight: 700; text-transform: uppercase; }
    </style>

    {{-- NAV PILLS --}}
    <div class="mb-4 nav-wrapper position-relative end-0">
        <ul class="p-1 nav nav-pills nav-fill" role="tablist">
            <li class="nav-item">
                <a class="nav-link mb-0 px-0 py-1 {{ ($tab ?? 'levels') === 'levels' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-levels" role="tab">
                    <i class="align-middle fa-solid fa-layer-group me-1"></i> Livelli
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-0 px-0 py-1 {{ ($tab ?? 'levels') === 'options' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-options" role="tab">
                    <i class="align-middle fa-solid fa-list-check me-1"></i> Opzioni
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-0 px-0 py-1 {{ ($tab ?? 'levels') === 'rules' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-rules" role="tab">
                    <i class="align-middle fa-solid fa-diagram-project me-1"></i> Regole
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-0 px-0 py-1 {{ ($tab ?? 'levels') === 'packages' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-packages" role="tab">
                    <i class="align-middle fa-solid fa-box-open me-1"></i> Pacchetti
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">

        {{-- TAB LIVELLI --}}
        <div class="tab-pane fade {{ ($tab ?? 'levels') === 'levels' ? 'show active' : '' }}" id="tab-levels" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 align-items-center">
                            <thead>
                                <tr>
                                    <th>Livello</th>
                                    <th>Titolo</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Min</th>
                                    <th class="text-center">Max</th>
                                    <th class="text-center">Attivo</th>
                                    <th class="text-center">Azione</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($levels as $lvl)
                                    <tr>
                                        <td><span class="text-sm fw-bold">L{{ $lvl->level }}</span></td>
                                        <td style="min-width: 300px;">
                                            <form method="POST" action="{{ route('quotes.levels.update', $lvl) }}">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="level" value="{{ $lvl->level }}">

                                                {{-- CORRETTO: value="{{ $lvl->name }}" e name="name" --}}
                                                <input type="text" name="name" class="px-2 border form-control form-control-sm" value="{{ $lvl->name }}" required>
                                        </td>
                                        <td class="text-center">
                                            <select name="selection_type" class="form-select form-select-sm">
                                                <option value="single" {{ $lvl->selection_type === 'single' ? 'selected' : '' }}>Single</option>
                                                <option value="multi" {{ $lvl->selection_type === 'multi' ? 'selected' : '' }}>Multi</option>
                                            </select>
                                        </td>
                                        <td><input type="number" name="min_select" class="text-center form-control form-control-sm" value="{{ $lvl->min_select }}"></td>
                                        <td><input type="number" name="max_select" class="text-center form-control form-control-sm" value="{{ $lvl->max_select }}"></td>
                                        <td class="text-center">
                                            <input type="hidden" name="is_active" value="0">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $lvl->is_active ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="submit" class="mb-0 btn btn-sm btn-outline-success"><i class="fa-solid fa-save"></i></button>
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

{{-- TAB OPZIONI --}}
<div class="tab-pane fade {{ ($tab ?? 'levels') === 'options' ? 'show active' : '' }}" id="tab-options" role="tabpanel">
    <div class="border shadow-none card">
        <div class="pb-0 card-header border-bottom">
            <form method="GET" action="{{ route('admin.quotes.index') }}" class="pb-3 row g-3 align-items-center">
                <input type="hidden" name="tab" value="options">
                <div class="col-auto">
                    <h6 class="mb-0">Seleziona Livello:</h6>
                </div>
                <div class="col-md-4">
                    <select name="level_id" class="px-2 border form-select">
                        @foreach ($levels as $lvl)
                            <option value="{{ $lvl->id }}" {{ (int)($selectedLevelId ?? 0) === (int)$lvl->id ? 'selected' : '' }}>
                                L{{ $lvl->level }} — {{ $lvl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="mb-0 btn btn-dark" type="submit">Gestisci Opzioni</button>
                </div>
            </form>
        </div>

        <div class="card-body">
            @if ($selectedLevel)
                {{-- FORM DI CREAZIONE PROFESSIONALE --}}
                <div class="p-3 mb-4 border rounded bg-light">
                    <h6 class="mb-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                        <i class="fa-solid fa-plus-circle me-1"></i> Aggiungi Nuova Opzione a: {{ $selectedLevel->name }}
                    </h6>
                    <form method="POST" action="{{ route('quotes.options.store') }}">
                        @csrf
                        <input type="hidden" name="level_id" value="{{ $selectedLevel->id }}">

                        {{-- RIGA 1: Nomi e Descrizioni --}}
                        <div class="mb-3 row">
                            <div class="col-md-4">
                                <label class="text-xs form-label fw-bold">Nome Opzione</label>
                                <input type="text" name="name" class="px-2 border form-control" placeholder="Es: Sito E-commerce" required>
                            </div>
                            <div class="col-md-8">
                                <label class="text-xs form-label fw-bold">Descrizione Commerciale (Visualizzata dall'utente)</label>
                                <input type="text" name="description" class="px-2 border form-control" placeholder="Descrivi brevemente il valore di questa scelta...">
                            </div>
                        </div>

                        {{-- RIGA 2: Valori Tecnici e Toggle --}}
                        <div class="row align-items-end g-3">
                            <div class="col-md-2">
                                <label class="text-xs form-label fw-bold">Ore Stimate</label>
                                <input type="number" name="hours" class="px-2 border form-control" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="text-xs form-label fw-bold">Prezzo (€)</label>
                                <input type="number" step="0.01" name="price" class="px-2 border form-control" value="0.00">
                            </div>
                            <div class="col-md-1">
                                <label class="text-xs form-label fw-bold">Posizione</label>
                                <input type="number" name="sort_order" class="px-2 border form-control" value="0">
                            </div>

                            {{-- SWITCHES --}}
                            <div class="col-md-3 border-start ps-4">
                                <div class="mb-2 d-flex justify-content-between">
                                    <label class="mb-0 text-xs form-check-label">Visibile (Attivo)</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <label class="mb-0 text-xs form-check-label">Pre-selezionato (Default)</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_default" value="1">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 border-start ps-4">
                                <div class="mb-2 d-flex justify-content-between text-primary">
                                    <label class="mb-0 text-xs form-check-label fw-bold">Obbligatorio (R)</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="pivot_is_required" value="1">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-warning">
                                    <label class="mb-0 text-xs form-check-label fw-bold">Nascosto (H)</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="pivot_is_hidden_by_default" value="1">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <button type="submit" class="mb-0 btn btn-success w-100">
                                    <i class="fa-solid fa-save me-1"></i> Crea Opzione
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- TABELLA ELENCO --}}
                <div class="mt-2 table-responsive">
                    <table class="table mb-0 table-bordered align-items-center">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 40%;">Dati Base (Nome e Valori)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stato</th>
                                <th class="text-center text-uppercase text-primary text-xxs font-weight-bolder opacity-7" style="background-color: #f8f9ff;">Logica Livello (Pivot)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azione</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($selectedLevel->options as $opt)
                                <tr>
                                    {{-- FORM DATI BASE --}}
                                    <form method="POST" action="{{ route('quotes.options.update', $opt) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="redirect_level_id" value="{{ $selectedLevel->id }}">
                                        <td>
                                            <input type="text" name="name" class="px-2 mb-1 border form-control form-control-sm fw-bold" value="{{ $opt->name }}">
                                            <textarea name="description" class="px-2 border form-control form-control-xs text-muted" rows="1">{{ $opt->description }}</textarea>
                                            <div class="gap-2 mt-2 d-flex">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light border-end-0 text-xxs">h</span>
                                                    <input type="number" name="hours" class="px-2 text-center border form-control" value="{{ $opt->hours }}">
                                                </div>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light border-end-0 text-xxs">€</span>
                                                    <input type="number" step="0.01" name="price" class="px-2 text-center border form-control" value="{{ $opt->price }}">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-block text-start">
                                                <div class="mb-1 form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $opt->is_active ? 'checked' : '' }}>
                                                    <label class="mb-0 text-xxs ms-1">Attivo</label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_default" value="1" {{ $opt->is_default ? 'checked' : '' }}>
                                                    <label class="mb-0 text-xxs ms-1">Default</label>
                                                </div>
                                            </div>
                                            <button type="submit" class="mt-2 btn btn-xs btn-outline-success d-block w-100">Salva Base</button>
                                        </td>
                                    </form>

                                    {{-- FORM PIVOT --}}
                                    <td style="background-color: #fcfcff;">
                                        <form method="POST" action="{{ route('quotes.levels.options.update', [$selectedLevel->id, $opt->id]) }}">
                                            @csrf @method('PUT')
                                            <div class="text-center">
                                                <div class="gap-3 mb-2 d-flex justify-content-center">
                                                    <div class="text-center">
                                                        <span class="text-xxs d-block fw-bold text-primary">Req (R)</span>
                                                        <div class="form-check form-switch d-inline-block">
                                                            <input class="form-check-input" type="checkbox" name="is_required" value="1" {{ $opt->pivot->is_required ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <span class="text-xxs d-block fw-bold text-warning">Hide (H)</span>
                                                        <div class="form-check form-switch d-inline-block">
                                                            <input class="form-check-input" type="checkbox" name="is_hidden_by_default" value="1" {{ $opt->pivot->is_hidden_by_default ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mx-auto mb-2 input-group input-group-sm" style="max-width: 80px;">
                                                    <span class="input-group-text bg-light border-end-0 text-xxs"><i class="fa-solid fa-sort"></i></span>
                                                    <input type="number" name="sort_order" class="px-2 text-center border form-control" value="{{ $opt->pivot->sort_order }}">
                                                </div>
                                                <button type="submit" class="btn btn-xs btn-dark">Applica Pivot</button>
                                            </div>
                                        </form>
                                    </td>

                                    <td class="text-center">
                                        <div class="gap-2 d-flex flex-column align-items-center">
                                            <form method="POST" action="{{ route('quotes.levels.options.detach', [$selectedLevel->id, $opt->id]) }}">
                                                @csrf @method('DELETE')
                                                <button class="p-0 mb-0 text-xs btn btn-link text-warning" title="Scollega da questo livello">
                                                    <i class="fa-solid fa-link-slash"></i> Scollega
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('quotes.options.destroy', $opt) }}" onsubmit="return confirm('Eliminare definitivamente dal DB?')">
                                                @csrf @method('DELETE')
                                                <button class="p-0 mb-0 text-xs btn btn-link text-danger" title="Elimina l'opzione">
                                                    <i class="fa-solid fa-trash"></i> Elimina
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
{{-- TAB REGOLE --}}
<div class="tab-pane fade {{ ($tab ?? 'levels') === 'rules' ? 'show active' : '' }}" id="tab-rules" role="tabpanel">
    <div class="card">
        <div class="pb-0 card-header">
            <form method="GET" action="{{ route('admin.quotes.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="rules">
                <div class="col-auto"><h6>Regole su:</h6></div>
                <div class="col-md-4">
                    <select name="rules_level_id" class="form-select">
                        @foreach ($levels as $lvl)
                            <option value="{{ $lvl->id }}" {{ (int)($rulesLevelId ?? 0) === (int)$lvl->id ? 'selected' : '' }}>
                                L{{ $lvl->level }} — {{ $lvl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto"><button class="mb-0 btn btn-dark" type="submit">Vai</button></div>
            </form>
        </div>

        <div class="card-body">
            @if ($rulesTargetLevel)
                <div class="p-3 mb-4 border rounded bg-light">
                    <h6 class="text-sm">Nuova regola per <strong>L{{ $rulesTargetLevel->level }}</strong></h6>
                    @if ($rulesTargetLevel->level > 1)
                        <form method="POST" action="{{ route('quotes.rules.store') }}" class="row g-2 align-items-end">
                            @csrf
                            <input type="hidden" name="target_level_id" value="{{ $rulesTargetLevel->id }}">

                            <div class="col-md-4">
                                <label class="text-xxs">Trigger (Se scegli):</label>
                                <select name="trigger_option_id" class="form-select form-select-sm" required>
                                    @foreach ($triggerLevels as $tl)
                                        <optgroup label="Livello {{ $tl->level }}">
                                            @foreach ($tl->options as $to)
                                                <option value="{{ $to->id }}">{{ $to->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="text-xxs">Azione:</label>
                                <select name="action_type" class="form-select form-select-sm">
                                    <option value="show_level">Mostra Livello</option>
                                    <option value="hide_level">Nascondi Livello</option>
                                    <option value="show_option">Mostra Opzione</option>
                                    <option value="hide_option">Nascondi Opzione</option>
                                    <option value="require_option">Rendi Obbligatoria</option>
                                    <option value="auto_select_option">Seleziona Autom.</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="text-xxs">Target (in L{{ $rulesTargetLevel->level }}):</label>
                                <select name="target_option_id" class="form-select form-select-sm">
                                    <option value="">Tutto il livello</option>
                                    @foreach ($rulesTargetOptions as $ro)
                                        <option value="{{ $ro->id }}">{{ $ro->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button type="submit" class="mb-0 btn btn-sm btn-success w-100">Crea Regola</button>
                            </div>
                        </form>
                    @else
                        <div class="p-2 alert alert-light text-warning">
                            <p class="mb-0 text-xs"><i class="fa-solid fa-circle-info me-1"></i> Il Livello 1 non può avere dipendenze da livelli precedenti.</p>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center">
                        <thead>
                            <tr>
                                <th>Se selezioni (Trigger)</th>
                                <th class="text-center">Azione</th>
                                <th>Effetto su (Target)</th>
                                <th class="text-center">Elimina</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rules as $r)
                                <tr>
                                    <td>
                                        <span class="text-sm fw-bold text-dark">{{ $r->triggerOption->name ?? 'N/A' }}</span><br>
                                        <small class="text-xxs text-secondary">Livello {{ $r->triggerLevel->level ?? '?' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-gradient-info text-xxs">
                                            {{ str_replace('_', ' ', $r->action_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm">
                                            @if($r->targetOption)
                                                <i class="text-xs fa-solid fa-caret-right me-1"></i>{{ $r->targetOption->name }}
                                            @else
                                                <i class="text-xs fa-solid fa-layer-group me-1 text-primary"></i>Tutto il Livello {{ $rulesTargetLevel->level }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('quotes.rules.destroy', $r) }}" onsubmit="return confirm('Eliminare questa regola?')">
                                            @csrf @method('DELETE')
                                            <button class="p-0 mb-0 btn btn-link text-danger" title="Elimina">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-sm text-center text-secondary">
                                        Nessuna regola definita per questo livello.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-5 text-center">
                    <i class="mb-3 fa-solid fa-diagram-project text-secondary" style="font-size: 2rem; opacity: 0.3;"></i>
                    <p class="text-secondary">Seleziona un livello dal menu sopra per gestire le regole di visibilità.</p>
                </div>
            @endif
        </div>
    </div>
</div>
        {{-- TAB PACCHETTI --}}
        <div class="tab-pane fade {{ ($tab ?? 'levels') === 'packages' ? 'show active' : '' }}" id="tab-packages" role="tabpanel">
            <div class="card">
                <div class="pb-0 card-header d-flex justify-content-between align-items-center">
                    <h6>Pacchetti</h6>
                    <a href="{{ route('quotes.packages.create') }}" class="btn btn-sm btn-dark">Nuovo</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th class="text-center">Prezzo</th>
                                    <th class="text-center">Stato</th>
                                    <th class="text-center">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($packages as $p)
                                    <tr>
                                        <td>
                                            <div class="px-2 py-1 d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="{{ $p->icon ?? 'fa-solid fa-box' }} text-dark" style="font-size: 18px;"></i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm font-weight-bold">{{ $p->name }}</h6>
                                                    <p class="mb-0 text-xs text-secondary">ID: #{{ $p->id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-success fw-bold">€{{ number_format($p->promo_price, 2, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $p->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                                {{ $p->is_active ? 'Attivo' : 'Off' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="gap-3 d-flex align-items-center justify-content-center">
                                                <a href="{{ route('quotes.packages.edit', $p) }}" class="p-0 btn btn-link text-info" title="Modifica">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <form method="POST" action="{{ route('quotes.packages.destroy', $p) }}" onsubmit="return confirm('Sei sicuro di voler eliminare definitivamente questo pacchetto?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-0 btn btn-link text-danger" title="Elimina">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-4 text-sm text-center text-secondary">Nessun pacchetto trovato.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
