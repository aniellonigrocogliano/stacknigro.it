@extends('layouts.admin')

@section('title', 'Contatti')

@section('content')
<div class="py-3 container-fluid">

  <div class="mb-3 d-flex justify-content-between align-items-center">
<h5 class="mb-0">
  <i class="fa-solid fa-address-book me-2"></i>Contatti
  <span id="sortStatus" class="text-xs ms-2 text-muted"></span>
</h5>
  </div>

  <div class="card">
    <div class="p-0 card-body">

      <div class="table-responsive">
        <table class="table mb-0 table-hover align-items-center">
          <thead>
            <tr>
              <th style="width:44px;"></th>
              <th style="width: 18%">Nome</th>
              <th style="width: 20%">Codice FontAwesome</th>
              <th style="width: 22%">Testo (visibile)</th>
              <th>Link (href)</th>
              <th class="text-center" style="width: 140px;">Nuova scheda</th>
              <th class="text-end" style="width: 120px">Azioni</th>
            </tr>
          </thead>

          <tbody id="contactsTbody">

            {{-- =========================
                 SEZIONE: NUOVO CONTATTO (NON DRAGGABILE)
                 ========================= --}}
            <tr class="table-light">
              <form id="f-create" method="POST" action="{{ route('contacts.store') }}">
                @csrf

                {{-- handle vuoto --}}
                <td></td>

                <td>
                  <input name="name"
                         class="form-control form-control-sm @error('name') is-invalid @enderror"
                         value="{{ old('name') }}"
                         placeholder="Es: WhatsApp">
                  @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </td>

                <td>
                  <input name="fa_icon"
                         class="form-control form-control-sm @error('fa_icon') is-invalid @enderror"
                         value="{{ old('fa_icon') }}"
                         placeholder="Es: fa-brands fa-whatsapp">
                  @error('fa_icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </td>

                <td>
                  <div class="gap-2 d-flex align-items-center">
                    <i class="{{ old('fa_icon') }}" style="min-width:18px"></i>
                    <input name="value"
                           class="form-control form-control-sm @error('value') is-invalid @enderror"
                           value="{{ old('value') }}"
                           placeholder="Es: +39... / email / @user">
                  </div>
                  @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </td>

                <td>
                  <input name="href"
                         class="form-control form-control-sm @error('href') is-invalid @enderror"
                         value="{{ old('href') }}"
                         placeholder="Es: mailto:... / tel:... / https://...">
                  @error('href') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </td>

                <td class="text-center">
                  <div class="m-0 form-check form-switch d-inline-flex align-items-center">
                    <input class="form-check-input"
                           type="checkbox"
                           name="target_blank"
                           id="create_target_blank"
                           value="1"
                           {{ old('target_blank', 1) ? 'checked' : '' }}>
                  </div>
                </td>

                <td class="text-end">
                  <button type="button"
                          class="p-0 btn btn-link text-success js-confirm"
                          title="Aggiungi"
                          data-title="Aggiungi contatto"
                          data-body="Confermi l'inserimento di questo contatto?"
                          data-form="f-create">
                    <i class="fa-solid fa-plus"></i>
                  </button>
                </td>
              </form>
            </tr>

            {{-- separatore “due tabelle” --}}
            <tr class="table-secondary">
              <td colspan="7" class="py-2">
                <div class="d-flex align-items-center">
                  <span class="me-2"><i class="fa-solid fa-grip-vertical"></i></span>
                  <strong>Contatti salvati</strong>
                  <span class="text-xs ms-2 text-muted">(trascina dal grip per cambiare ordine)</span>
                </div>
              </td>
            </tr>

            {{-- =========================
                 SEZIONE: CONTATTI ESISTENTI (DRAGGABILI)
                 ========================= --}}
            @forelse($contacts as $c)
              <tr data-id="{{ $c->id }}" draggable="true" class="js-draggable-row">
                {{-- UPDATE --}}
                <form id="f-update-{{ $c->id }}" method="POST" action="{{ route('contacts.update', $c) }}">
                  @csrf
                  @method('PUT')

                  {{-- HANDLE: drag solo da qui --}}
                  <td class="text-center align-middle">
                    <span class="js-drag-handle" draggable="true" style="cursor: grab; user-select:none;">
                      <i class="fa-solid fa-grip-vertical text-secondary"></i>
                    </span>
                  </td>

                  <td>
                    <input name="name" class="form-control form-control-sm" value="{{ $c->name }}">
                  </td>

                  <td>
                    <input name="fa_icon" class="form-control form-control-sm" value="{{ $c->fa_icon }}">
                  </td>

                  <td>
                    <div class="gap-2 d-flex align-items-center">
                      @if($c->fa_icon)
                        <i class="{{ $c->fa_icon }}" style="min-width:18px"></i>
                      @else
                        <span style="min-width:18px"></span>
                      @endif

                      <input name="value" class="form-control form-control-sm" value="{{ $c->value }}">
                    </div>
                  </td>

                  <td>
                    <input name="href"
                           class="form-control form-control-sm"
                           value="{{ $c->href }}"
                           placeholder="mailto: / tel: / https://">
                  </td>

                  <td class="text-center">
                    <div class="m-0 form-check form-switch d-inline-flex align-items-center">
                      <input class="form-check-input"
                             type="checkbox"
                             name="target_blank"
                             id="tb-{{ $c->id }}"
                             value="1"
                             {{ $c->target_blank ? 'checked' : '' }}>
                    </div>
                  </td>

                  {{-- AZIONI: salva + elimina --}}
                  <td class="text-end" onclick="event.stopPropagation()">
                    <div class="gap-3 d-flex justify-content-end">

                      <button type="button"
                              class="p-0 btn btn-link text-success js-confirm"
                              title="Salva"
                              data-title="Salva modifiche"
                              data-body="Vuoi salvare le modifiche a '{{ $c->name }}'?"
                              data-form="f-update-{{ $c->id }}">
                        <i class="fa-solid fa-floppy-disk"></i>
                      </button>

                      <button type="button"
                              class="p-0 btn btn-link text-danger js-confirm"
                              title="Elimina"
                              data-title="Elimina contatto"
                              data-body="Eliminare definitivamente '{{ $c->name }}'?"
                              data-form="f-delete-{{ $c->id }}">
                        <i class="fa-solid fa-trash"></i>
                      </button>

                    </div>
                  </td>
                </form>

                {{-- FORM DELETE separato --}}
                <form id="f-delete-{{ $c->id }}" method="POST" action="{{ route('contacts.destroy', $c) }}">
                  @csrf
                  @method('DELETE')
                </form>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="py-4 text-center text-muted">
                  Nessun contatto salvato
                </td>
              </tr>
            @endforelse

          </tbody>
        </table>
      </div>

    </div>
  </div>

  {{-- MODAL CONFERMA --}}
  <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-normal" id="confirmActionTitle">Conferma</h5>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="confirmActionBody">Sei sicuro?</div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="button" class="btn bg-gradient-danger" id="confirmActionSubmit">Conferma</button>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {

    // =========================
    // MICRO TOAST (status)
    // =========================
    const statusEl = document.getElementById('sortStatus');
    function setStatus(text, kind = 'muted') {
      if (!statusEl) return;
      statusEl.className = 'ms-2 text-xs text-' + kind;
      statusEl.textContent = text || '';
    }

    // =========================
    // MODAL CONFERMA (se bootstrap c'è)
    // =========================
    const modalEl = document.getElementById('confirmActionModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
      const modal = new bootstrap.Modal(modalEl);
      const titleEl = document.getElementById('confirmActionTitle');
      const bodyEl  = document.getElementById('confirmActionBody');
      const submitBtn = document.getElementById('confirmActionSubmit');

      let pendingFormId = null;

      document.querySelectorAll('.js-confirm').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          pendingFormId = btn.getAttribute('data-form');
          titleEl.textContent = btn.getAttribute('data-title') || 'Conferma';
          bodyEl.textContent  = btn.getAttribute('data-body')  || 'Sei sicuro?';
          modal.show();
        });
      });

      submitBtn.addEventListener('click', () => {
        if (!pendingFormId) return;
        const form = document.getElementById(pendingFormId);
        if (form) form.submit();
      });

      modalEl.addEventListener('hidden.bs.modal', () => {
        pendingFormId = null;
      });
    }

    // =========================
    // DRAG & DROP (vanilla) - SOLO DA HANDLE
    // =========================
    const tbody = document.getElementById('contactsTbody');
    if (!tbody) return;

    let dragRow = null;

    function getOrderedIdsForSave() {
      return Array.from(tbody.querySelectorAll('tr[data-id]'))
        .map(tr => parseInt(tr.getAttribute('data-id'), 10))
        .filter(n => Number.isFinite(n));
    }

    async function saveOrder() {
      const url = "{{ route('contacts.reorder') }}";
      const ids = getOrderedIdsForSave();

      setStatus('Salvataggio ordine...', 'secondary');

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
          },
          body: JSON.stringify({ ids })
        });

        if (!res.ok) throw new Error('HTTP ' + res.status);

        setStatus('Ordine salvato ✓', 'success');
        setTimeout(() => setStatus(''), 1400);
      } catch (e) {
        console.error(e);
        setStatus('Errore salvataggio ordine', 'danger');
      }
    }

    // Drag start SOLO sul handle (IMPORTANTE: setData per Firefox)
    tbody.querySelectorAll('.js-drag-handle[draggable="true"]').forEach(handle => {
      handle.addEventListener('dragstart', (e) => {
        const tr = e.target.closest('tr[data-id]');
        if (!tr) return;

        dragRow = tr;
        tr.classList.add('opacity-50');

        // ✅ Firefox / compat: senza setData spesso non parte il drag
        try { e.dataTransfer.setData('text/plain', tr.getAttribute('data-id') || ''); } catch (_) {}
        e.dataTransfer.effectAllowed = 'move';
      });

      handle.addEventListener('dragend', () => {
        if (dragRow) dragRow.classList.remove('opacity-50');
        dragRow = null;
      });
    });

    // Permetti drop sulle righe draggabili
    tbody.querySelectorAll('tr[data-id]').forEach(tr => {
      tr.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
      });

      tr.addEventListener('drop', async (e) => {
        e.preventDefault();
        if (!dragRow || dragRow === tr) return;

        const rect = tr.getBoundingClientRect();
        const isAfter = (e.clientY - rect.top) > (rect.height / 2);

        if (isAfter) tr.after(dragRow);
        else tr.before(dragRow);

        await saveOrder();
      });
    });

  });
</script>
@endpush
