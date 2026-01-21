@extends('layouts.admin')

@section('title', 'Regole preventivo')

@section('content')
<div class="py-4 container-fluid">

  <div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
      <h4 class="mb-0">Regole preventivo</h4>
      <p class="mb-0 text-sm text-secondary">
        Qui definisci dipendenze tra scelte: mostra/nascondi/obbligatoria/ore/prezzi.
      </p>
    </div>

    <a href="{{ route('admin.quote-rules.create') }}" class="mb-0 btn bg-gradient-dark">
      <i class="fa fa-plus me-2"></i> Nuova regola
    </a>
  </div>

  @if (session('success'))
    <div class="text-white alert alert-success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="text-white alert alert-danger">
      <strong>Ci sono errori:</strong>
      <ul class="mt-2 mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="mb-4 card">
    <div class="pb-0 card-header">
      <div class="row align-items-center">
        <div class="col-lg-7 col-12">
          <h6 class="mb-0">Elenco regole</h6>
          <p class="mb-0 text-sm text-secondary">
            Totali: <strong>{{ $rules->total() }}</strong>
          </p>
        </div>

        <div class="mt-3 col-lg-5 col-12 mt-lg-0">
          {{-- FILTRI --}}
          <form method="GET" action="{{ route('admin.quote-rules.index') }}" class="gap-2 d-flex justify-content-lg-end">

            <select name="action_type" class="form-select form-select-sm" style="max-width: 220px;">
              <option value="">Tutte azioni</option>
              @foreach($actionTypes as $type)
                <option value="{{ $type }}" {{ request('action_type') === $type ? 'selected' : '' }}>
                  {{ $type }}
                </option>
              @endforeach
            </select>

            <select name="trigger_level_id" class="form-select form-select-sm" style="max-width: 220px;">
              <option value="">Tutti i livelli</option>
              @foreach($levels as $lvl)
                <option value="{{ $lvl->id }}" {{ (string)request('trigger_level_id') === (string)$lvl->id ? 'selected' : '' }}>
                  L{{ $lvl->level ?? $lvl->position ?? $lvl->sort_order ?? $lvl->id }} — {{ $lvl->title ?? $lvl->name ?? ('Livello '.$lvl->id) }}
                </option>
              @endforeach
            </select>

            <button class="mb-0 btn btn-sm bg-gradient-dark" type="submit">
              Filtra
            </button>

            @if(request()->hasAny(['action_type','trigger_level_id']))
              <a class="mb-0 btn btn-sm btn-outline-dark" href="{{ route('admin.quote-rules.index') }}">
                Reset
              </a>
            @endif
          </form>
        </div>
      </div>
    </div>

    <div class="px-0 pb-2 card-body">
      <div class="table-responsive">
        <table class="table mb-0 align-items-center">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Se (trigger)</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Azione</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Allora (target)</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Attiva</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azioni</th>
            </tr>
          </thead>

          <tbody>
            @forelse($rules as $rule)
              @php
                $type = $rule->action_type ?? '—';

                $badge = match($type) {
                  'show_level' => 'bg-gradient-success',
                  'hide_level' => 'bg-gradient-secondary',
                  'require_option' => 'bg-gradient-warning',
                  'auto_select_option' => 'bg-gradient-info',
                  'add_hours','add_price' => 'bg-gradient-dark',
                  'set_hours','set_price' => 'bg-gradient-primary',
                  default => 'bg-gradient-dark'
                };

                $isActive = (int)($rule->is_active ?? 1) === 1;

                $triggerLabel = $rule->triggerOption?->label ?? ('Option #'.($rule->trigger_option_id ?? '—'));
                $triggerLevel = $rule->triggerLevel?->title ?? $rule->triggerLevel?->name ?? '';

                $targetLabel = $rule->targetOption?->label
                  ?? ($rule->targetLevel?->title ?? $rule->targetLevel?->name ?? '—');
              @endphp

              <tr>
                <td>
                  <div class="px-2 py-1">
                    <div class="text-sm fw-bold text-dark">{{ $triggerLabel }}</div>
                    <div class="text-xs text-secondary">{{ $triggerLevel }}</div>
                  </div>
                </td>

                <td class="align-middle">
                  <span class="badge badge-sm {{ $badge }}">{{ $type }}</span>
                </td>

                <td>
                  <div class="px-2 py-1">
                    <div class="text-sm fw-bold text-dark">{{ $targetLabel }}</div>
                    @if(!is_null($rule->value_min) || !is_null($rule->value_max))
                      <div class="text-xs text-secondary">
                        min: {{ $rule->value_min ?? '—' }} — max: {{ $rule->value_max ?? '—' }}
                      </div>
                    @endif
                  </div>
                </td>

                <td class="text-center align-middle">
                  @if($isActive)
                    <span class="badge badge-sm bg-gradient-success">Sì</span>
                  @else
                    <span class="badge badge-sm bg-gradient-secondary">No</span>
                  @endif
                </td>

                <td class="text-center align-middle">
                  <a href="{{ route('admin.quote-rules.edit', $rule) }}" class="px-2 mb-0 btn btn-link text-dark" title="Modifica">
                    <i class="fa fa-pen"></i>
                  </a>

                  <button
                    type="button"
                    class="px-2 mb-0 btn btn-link text-danger"
                    title="Elimina"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmDeleteModal"
                    data-action="{{ route('admin.quote-rules.destroy', $rule) }}"
                    data-title="Eliminare questa regola?"
                    data-body="Confermi eliminazione della regola '{{ $type }}'?"
                  >
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-4 text-center text-secondary">
                  Nessuna regola inserita.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-4 pt-3">
        {{ $rules->links() }}
      </div>
    </div>
  </div>

</div>

{{-- MODAL conferma delete --}}
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

