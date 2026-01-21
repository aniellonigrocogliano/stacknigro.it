@extends('layouts.admin')

@section('title', 'Nuova regola')

@section('content')
<div class="py-4 container-fluid">

  <div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
      <h4 class="mb-0">Nuova regola</h4>
      <p class="mb-0 text-sm text-secondary">Crea una dipendenza tra scelte (mostra/nascondi/obbligatoria/ore/prezzi).</p>
    </div>

    <a href="{{ route('admin.quote-rules.index') }}" class="mb-0 btn btn-outline-dark">
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

  <form method="POST" action="{{ route('admin.quote-rules.store') }}" id="ruleForm">
    @csrf

    <div class="row">
      <div class="col-lg-8">

        {{-- CONDIZIONE --}}
        <div class="mb-4 card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Condizione</h6>
            <p class="mb-0 text-sm text-secondary">Definisci quando scatta la regola.</p>
          </div>
          <div class="card-body">

            <div class="mb-3">
              <label class="form-label">Seleziona livello trigger</label>
              <select name="trigger_level_id" id="trigger_level_id" class="form-select" required>
                <option value="">— Seleziona livello —</option>
                @foreach($levels as $lvl)
                  @php
                    $lvlLabel = 'L'.($lvl->level ?? $lvl->position ?? $lvl->sort_order ?? $lvl->id).' — '.($lvl->title ?? $lvl->name ?? ('Livello '.$lvl->id));
                  @endphp
                  <option value="{{ $lvl->id }}" {{ (string)old('trigger_level_id') === (string)$lvl->id ? 'selected' : '' }}>
                    {{ $lvlLabel }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="mb-0">
              <label class="form-label">Se l’utente seleziona (opzione trigger)</label>
              <select name="trigger_option_id" id="trigger_option_id" class="form-select" required>
                <option value="">— Seleziona opzione —</option>
                {{-- popolata via JS in base al livello --}}
              </select>
              <small class="mt-1 text-xs text-secondary d-block">Prima scegli il livello, poi l’opzione.</small>
            </div>

          </div>
        </div>

        {{-- AZIONE --}}
        <div class="mb-4 card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Azione</h6>
            <p class="mb-0 text-sm text-secondary">Cosa succede quando la condizione è vera.</p>
          </div>
          <div class="card-body">

            <div class="mb-3">
              <label class="form-label">Allora (tipo azione)</label>
              <select name="action_type" id="action_type" class="form-select" required>
                <option value="">— Seleziona azione —</option>
                @foreach($actionTypes as $type)
                  <option value="{{ $type }}" {{ old('action_type') === $type ? 'selected' : '' }}>
                    {{ $actionTypeLabels[$type] ?? $type }}
                  </option>
                @endforeach
              </select>
              <small class="mt-1 text-xs text-secondary d-block">
                “Obbligatoria” = nel frontend verrà auto-selezionata e non deselezionabile (implementazione lato frontend).
              </small>
            </div>

            {{-- TARGET LIVELLO (show/hide) --}}
            <div class="mb-3" id="targetLevelWrap" style="display:none;">
              <label class="form-label">Livello target</label>
              <select name="target_level_id" id="target_level_id" class="form-select">
                <option value="">— Seleziona livello target —</option>
                @foreach($levels as $lvl)
                  @php
                    $lvlLabel = 'L'.($lvl->level ?? $lvl->position ?? $lvl->sort_order ?? $lvl->id).' — '.($lvl->title ?? $lvl->name ?? ('Livello '.$lvl->id));
                  @endphp
                  <option value="{{ $lvl->id }}" {{ (string)old('target_level_id') === (string)$lvl->id ? 'selected' : '' }}>
                    {{ $lvlLabel }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- TARGET OPZIONE (require/auto_select) --}}
            <div class="mb-3" id="targetOptionWrap" style="display:none;">
              <label class="form-label">Opzione target</label>
              <select name="target_option_id" id="target_option_id" class="form-select">
                <option value="">— Seleziona opzione target —</option>
                @foreach($options as $o)
                  <option value="{{ $o->id }}" {{ (string)old('target_option_id') === (string)$o->id ? 'selected' : '' }}>
                    {{ $o->label ?? ('Opzione #'.$o->id) }}
                  </option>
                @endforeach
              </select>
              <small class="mt-1 text-xs text-secondary d-block">
                Qui puoi scegliere qualsiasi opzione (vale anche per più livelli).
              </small>
            </div>

            {{-- VALORI (ore/prezzi) --}}
            <div class="row" id="valuesWrap" style="display:none;">
              <div class="mb-3 col-md-6">
                <label class="form-label">Valore min</label>
                <input type="number" min="0" name="value_min" class="form-control" value="{{ old('value_min') }}">
              </div>
              <div class="mb-3 col-md-6">
                <label class="form-label">Valore max</label>
                <input type="number" min="0" name="value_max" class="form-control" value="{{ old('value_max') }}">
              </div>
              <div class="col-12">
                <small class="text-xs text-secondary d-block">
                  Per ore/prezzi puoi mettere un range (min/max). Se ti serve solo un valore, compila solo min.
                </small>
              </div>
            </div>

          </div>
        </div>

      </div>

      {{-- SIDEBAR --}}
      <div class="col-lg-4">

        <div class="mb-4 card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Stato</h6>
          </div>
          <div class="card-body">

            <div class="mb-3 d-flex align-items-center justify-content-between">
              <div>
                <label class="mb-0 form-label">Attiva</label>
                <div class="text-xs text-secondary">Se disattiva, la regola non viene considerata.</div>
              </div>
              <div class="m-0 form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
              </div>
            </div>

            <div class="mb-0">
              <label class="form-label">Ordine</label>
              <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
            </div>

          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <button class="mb-0 btn bg-gradient-dark w-100" type="submit">
              <i class="fa-solid fa-floppy-disk me-2"></i> Salva regola
            </button>
          </div>
        </div>

      </div>
    </div>
  </form>

</div>
@endsection

@push('scripts')
<script>
  const optionsByLevel = @json($optionsByLevel);

  const triggerLevel  = document.getElementById('trigger_level_id');
  const triggerOption = document.getElementById('trigger_option_id');

  const actionType = document.getElementById('action_type');
  const targetLevelWrap  = document.getElementById('targetLevelWrap');
  const targetOptionWrap = document.getElementById('targetOptionWrap');
  const valuesWrap       = document.getElementById('valuesWrap');

  function fillOptions(selectEl, items, selectedId = null) {
    // mantiene la prima option placeholder
    const first = selectEl.querySelector('option[value=""]');
    selectEl.innerHTML = '';
    if (first) selectEl.appendChild(first);

    (items || []).forEach(item => {
      const opt = document.createElement('option');
      opt.value = item.id;
      opt.textContent = item.label;
      if (selectedId && String(selectedId) === String(item.id)) opt.selected = true;
      selectEl.appendChild(opt);
    });
  }

  function updateTriggerOptions() {
    const lvlId = triggerLevel.value;
    const selected = "{{ old('trigger_option_id') }}";
    const list = optionsByLevel[lvlId] || [];
    fillOptions(triggerOption, list, selected);
  }

  function updateActionUI() {
    const t = actionType.value;

    const needsTargetLevel  = (t === 'show_level' || t === 'hide_level');
    const needsTargetOption = (t === 'require_option' || t === 'auto_select_option');
    const needsValues       = (t === 'add_hours' || t === 'add_price' || t === 'set_hours' || t === 'set_price');

    targetLevelWrap.style.display  = needsTargetLevel ? '' : 'none';
    targetOptionWrap.style.display = needsTargetOption ? '' : 'none';
    valuesWrap.style.display       = needsValues ? '' : 'none';
  }

  document.addEventListener('DOMContentLoaded', () => {
    // trigger options
    updateTriggerOptions();
    triggerLevel.addEventListener('change', updateTriggerOptions);

    // action ui
    updateActionUI();
    actionType.addEventListener('change', updateActionUI);
  });
</script>
@endpush
