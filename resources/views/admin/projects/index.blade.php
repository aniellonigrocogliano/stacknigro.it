@extends('layouts.admin')

@section('title', 'Progetti')

@section('content')
<div class="mb-4 row">
  <div class="mb-4 col-12">
    <div class="card">

      <div class="pb-0 card-header">
        <div class="row align-items-center">
          <div class="col-lg-6 col-7">
            <h6 class="mb-0">
              Progetti
              <span id="sortStatus" class="text-xs ms-2 text-muted"></span>
            </h6>
            <p class="mb-0 text-sm">
              <i class="fa fa-folder-open text-info" aria-hidden="true"></i>
              <span class="font-weight-bold ms-1">{{ $projects->count() }}</span> totali
              <span class="text-xs ms-2 text-muted">(trascina dal grip per cambiare ordine)</span>
            </p>
          </div>

          <div class="my-auto col-lg-6 col-5 text-end">
            <a href="{{ route('admin.projects.create') }}" class="mb-0 btn btn-success">
              <i class="fa fa-plus me-1" aria-hidden="true"></i> Aggiungi progetto
            </a>
          </div>
        </div>
      </div>

      <div class="px-0 pb-2 card-body">
        <div class="table-responsive">
          <table class="table mb-0 align-items-center table-hover">
            <thead>
              <tr>
                <th style="width:44px;"></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Progetto</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pubblicato</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Immagini</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azioni</th>
              </tr>
            </thead>

            <tbody id="projectsTbody">
              @forelse($projects as $project)
                <tr data-id="{{ $project->id }}" draggable="true" class="js-draggable-row">
                  {{-- HANDLE --}}
                  <td class="text-center align-middle">
                    <span class="js-drag-handle" draggable="true" style="cursor: grab; user-select:none;">
                      <i class="fa-solid fa-grip-vertical text-secondary"></i>
                    </span>
                  </td>

                  <td>
                    <div class="px-2 py-1 d-flex">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $project->title }}</h6>
                        <p class="mb-0 text-xs text-secondary">{{ $project->slug }}</p>
                      </div>
                    </div>
                  </td>

                  <td class="align-middle">
                    @if($project->is_published)
                      <span class="badge badge-sm bg-gradient-success">Sì</span>
                    @else
                      <span class="badge badge-sm bg-gradient-secondary">No</span>
                    @endif
                  </td>

                  <td class="align-middle">
                    <span class="text-sm font-weight-bold">
                      {{ $project->images_count }}
                    </span>
                  </td>

                  <td class="text-center align-middle" onclick="event.stopPropagation()">
                    <a href="{{ route('admin.projects.edit', $project) }}"
                       class="px-2 mb-0 btn btn-link text-dark"
                       title="Modifica">
                      <i class="fa fa-pen"></i>
                    </a>

                    <button
                      type="button"
                      class="px-2 mb-0 btn btn-link text-danger"
                      title="Elimina"
                      data-bs-toggle="modal"
                      data-bs-target="#confirmDeleteModal"
                      data-action="{{ route('admin.projects.destroy', $project) }}"
                      data-title="Eliminare progetto?"
                      data-body="Vuoi eliminare il progetto '{{ $project->title }}'? Verranno eliminate anche tutte le immagini associate."
                    >
                      <i class="fa fa-trash"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-4 text-center text-secondary">
                    Nessun progetto inserito.
                  </td>
                </tr>
              @endforelse
            </tbody>

          </table>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- MODAL CONFERMA ELIMINAZIONE --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-normal" id="confirmDeleteTitle">Conferma eliminazione</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="confirmDeleteBody">
        Sei sicuro di voler eliminare questo progetto?
      </div>

      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">
          Annulla
        </button>

        <form method="POST" id="confirmDeleteForm">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn bg-gradient-danger">
            Elimina
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {

    // =========================
    // MODAL DELETE (già tuo)
    // =========================
    const modalEl = document.getElementById('confirmDeleteModal');
    if (modalEl) {
      modalEl.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        if (!button) return;

        document.getElementById('confirmDeleteForm').action =
          button.getAttribute('data-action');

        document.getElementById('confirmDeleteTitle').innerText =
          button.getAttribute('data-title') || 'Conferma eliminazione';

        document.getElementById('confirmDeleteBody').innerText =
          button.getAttribute('data-body') || 'Sei sicuro?';
      });
    }

    // =========================
    // MICRO STATUS
    // =========================
    const statusEl = document.getElementById('sortStatus');
    function setStatus(text, kind = 'muted') {
      if (!statusEl) return;
      statusEl.className = 'text-xs ms-2 text-' + kind;
      statusEl.textContent = text || '';
    }

    // =========================
    // DRAG & DROP - SOLO HANDLE
    // =========================
    const tbody = document.getElementById('projectsTbody');
    if (!tbody) return;

    let dragRow = null;

    function getOrderedIdsForSave() {
      return Array.from(tbody.querySelectorAll('tr[data-id]'))
        .map(tr => parseInt(tr.getAttribute('data-id'), 10))
        .filter(n => Number.isFinite(n));
    }

    async function saveOrder() {
      const url = "{{ route('admin.projects.reorder') }}";
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

    // dragstart SOLO sul handle (setData per Firefox)
    tbody.querySelectorAll('.js-drag-handle[draggable="true"]').forEach(handle => {
      handle.addEventListener('dragstart', (e) => {
        const tr = e.target.closest('tr[data-id]');
        if (!tr) return;

        dragRow = tr;
        tr.classList.add('opacity-50');

        try { e.dataTransfer.setData('text/plain', tr.getAttribute('data-id') || ''); } catch (_) {}
        e.dataTransfer.effectAllowed = 'move';
      });

      handle.addEventListener('dragend', () => {
        if (dragRow) dragRow.classList.remove('opacity-50');
        dragRow = null;
      });
    });

    // drop sulle righe
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
