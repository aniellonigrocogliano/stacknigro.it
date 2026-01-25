@extends('layouts.admin')

@section('title', 'Contatti')

@section('content')
<div class="py-3 container-fluid">

  <div class="mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
      <i class="fa-solid fa-address-book me-2"></i>Contatti
    </h5>
  </div>

  <div class="card">
    <div class="p-0 card-body">

      <div class="table-responsive">
        <table class="table mb-0 table-hover align-items-center">
          <thead>
            <tr>
              <th style="width: 26%">Nome</th>
              <th style="width: 26%">Codice FontAwesome</th>
              <th>Contatto</th>
              <th class="text-end" style="width: 120px">Azioni</th>
            </tr>
          </thead>

          <tbody>
            {{-- PRIMA RIGA: INSERT --}}
            <tr>
              <form id="f-create" method="POST" action="{{ route('contacts.store') }}">
                @csrf

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
                           placeholder="Es: +39... / email / link">
                  </div>
                  @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

            {{-- RIGHE ESISTENTI --}}
            @forelse($contacts as $c)
              <tr>
                {{-- UPDATE --}}
                <form id="f-update-{{ $c->id }}" method="POST" action="{{ route('contacts.update', $c) }}">
                  @csrf
                  @method('PUT')

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

                  {{-- AZIONI: salva + elimina --}}
                  <td class="text-end" onclick="event.stopPropagation()">
                    <div class="gap-3 d-flex justify-content-end">

                      {{-- SALVA --}}
                      <button type="button"
                              class="p-0 btn btn-link text-success js-confirm"
                              title="Salva"
                              data-title="Salva modifiche"
                              data-body="Vuoi salvare le modifiche a '{{ $c->name }}'?"
                              data-form="f-update-{{ $c->id }}">
                        <i class="fa-solid fa-floppy-disk"></i>
                      </button>

                      {{-- ELIMINA (form separato) --}}
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

                {{-- FORM DELETE separato ma nella stessa riga (non duplico righe) --}}
                <form id="f-delete-{{ $c->id }}" method="POST" action="{{ route('contacts.destroy', $c) }}">
                  @csrf
                  @method('DELETE')
                </form>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="py-4 text-center text-muted">
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
