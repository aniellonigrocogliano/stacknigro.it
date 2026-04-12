@extends('layouts.admin')

@section('title', 'Cestino')

@section('content')
<div class="py-3 container-fluid">

  <div class="mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
      <i class="fa-solid fa-trash me-2"></i>Cestino
    </h5>

    <div class="gap-2 d-flex">
      <a href="{{ route('inbox.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-inbox me-1"></i> Inbox
      </a>

      <a href="{{ route('inbox.archive') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-box-archive me-1"></i> Archivio
      </a>

      {{-- Svuota cestino --}}
      <form id="f-empty-trash" method="POST" action="{{ route('inbox.trash.empty') }}">
        @csrf
        @method('DELETE')
        <button
          type="button"
          class="btn btn-danger btn-sm js-confirm"
          data-title="Svuota cestino"
          data-body="Vuoi svuotare il cestino? Questa azione elimina definitivamente tutti i messaggi nel cestino."
          data-form="f-empty-trash"
        >
          <i class="fa-solid fa-broom me-1"></i> Svuota cestino
        </button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="p-0 card-body">

      <div class="table-responsive">
        <table class="table mb-0 table-hover align-items-center">
          <thead>
            <tr>
              <th>Da</th>
              <th>Oggetto</th>
              <th>Sorgente</th>
              <th class="text-end">Eliminato il</th>
              <th class="text-end">Azioni</th>
            </tr>
          </thead>
          <tbody>

          @forelse($conversations as $c)
            <tr>
              <td class="text-sm">
                {{ $c->name }}<br>
                <span class="text-xs text-muted">{{ $c->email }}</span>
              </td>

              <td class="text-sm">
                {{ $c->subject ?: '—' }}
              </td>

<td class="text-sm">
                @if($c->source === 'quote')
                  <i class="fa-solid fa-calculator me-1"></i>Preventivo
                @elseif($c->source === 'package')
                  <i class="fa-solid fa-box me-1"></i>Pacchetto
                @else
                  <i class="fa-solid fa-envelope me-1"></i>Contatto
                @endif
              </td>

              <td class="text-sm text-end">
                {{ optional($c->deleted_at)->format('d/m/Y H:i') }}
              </td>

              <td class="text-end">
                <div class="gap-3 d-flex justify-content-end">

                  {{-- Ripristina --}}
                  <form id="f-restore-{{ $c->id }}" method="POST" action="{{ route('inbox.restore', $c->id) }}">
                    @csrf
                    @method('PATCH')
                    <button
                      type="button"
                      class="p-0 btn btn-link js-confirm text-success"
                      title="Ripristina"
                      data-title="Ripristina messaggio"
                      data-body="Vuoi ripristinare questo messaggio (torna in Inbox)?"
                      data-form="f-restore-{{ $c->id }}"
                    >
                      <i class="fa-solid fa-rotate-left"></i>
                    </button>
                  </form>

                  {{-- Elimina definitivo --}}
                  <form id="f-force-{{ $c->id }}" method="POST" action="{{ route('inbox.forceDelete', $c->id) }}">
                    @csrf
                    @method('DELETE')
                    <button
                      type="button"
                      class="p-0 btn btn-link text-danger js-confirm"
                      title="Elimina definitivamente"
                      data-title="Eliminazione definitiva"
                      data-body="Eliminare definitivamente questo messaggio? Azione irreversibile."
                      data-form="f-force-{{ $c->id }}"
                    >
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </form>

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-4 text-center text-muted">
                Il cestino è vuoto
              </td>
            </tr>
          @endforelse

          </tbody>
        </table>
      </div>

    </div>
  </div>

  <div class="mt-3">
    {{ $conversations->links() }}
  </div>

  {{-- MODAL CONFERMA AZIONE (riusato per TUTTO) --}}
  <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-normal" id="confirmActionTitle">Conferma</h5>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="confirmActionBody">Sei sicuro?</div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">
            Annulla
          </button>
          <button type="button" class="btn bg-gradient-danger" id="confirmActionSubmit">
            Conferma
          </button>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('confirmActionModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;

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
  });
</script>
@endpush
