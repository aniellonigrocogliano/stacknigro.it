@extends('layouts.admin')

@section('title', 'Livelli preventivatore')

@section('content')
<div class="container-fluid py-4">

  {{-- Header --}}
  <div class="row mb-4">
    <div class="col-lg-10 col-md-12 mb-md-0">
      <div class="card">

        <div class="card-header pb-0">
          <div class="row">
            <div class="col-lg-6 col-7">
              <h6>Livelli preventivatore</h6>
              <p class="text-sm mb-0">
                <i class="fa fa-check text-info" aria-hidden="true"></i>
                <span class="font-weight-bold ms-1">{{ $levels->count() }}</span> livelli
              </p>
            </div>

            <div class="col-lg-6 col-5 my-auto text-end">
              <a href="{{ route('admin.quote-levels.create') }}" class="btn bg-gradient-dark mb-0">
                <i class="fa fa-plus me-1" aria-hidden="true"></i> Aggiungi livello
              </a>
            </div>
          </div>
        </div>

        {{-- Flash success --}}
        @if(session('success'))
          <div class="px-4 pt-3">
            <div class="alert alert-success text-white mb-0" role="alert">
              {{ session('success') }}
            </div>
          </div>
        @endif

        <div class="card-body px-0 pb-2">
          <div class="table-responsive">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ordine</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nome</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipo</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Obbligatorio</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Attivo</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azioni</th>
                </tr>
              </thead>

              <tbody>
                @forelse($levels as $level)
                  @php
                    $typeLabel = $level->selection_type === 'single' ? 'Scelta unica' : 'Scelta multipla';
                    $typeBadge = $level->selection_type === 'single' ? 'bg-gradient-info' : 'bg-gradient-dark';
                  @endphp

                  <tr>
                    <td class="align-middle">
                      <span class="text-sm font-weight-bold px-3">{{ $level->sort_order }}</span>
                    </td>

                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ $level->name }}</h6>
                          <p class="mb-0 text-xs text-secondary">
                            ID: {{ $level->id }}
                          </p>
                        </div>
                      </div>
                    </td>

                    <td class="align-middle">
                      <span class="badge badge-sm {{ $typeBadge }}">{{ $typeLabel }}</span>
                      <span class="text-xs text-secondary ms-2">{{ $level->selection_type }}</span>
                    </td>

                    <td class="align-middle">
                      @if((int) $level->is_required === 1)
                        <span class="badge badge-sm bg-gradient-warning text-dark">Sì</span>
                      @else
                        <span class="badge badge-sm bg-gradient-secondary">No</span>
                      @endif
                    </td>

                    <td class="align-middle">
                      @if((int) $level->is_active === 1)
                        <span class="badge badge-sm bg-gradient-success">Attivo</span>
                      @else
                        <span class="badge badge-sm bg-gradient-secondary">Off</span>
                      @endif
                    </td>

                    <td class="align-middle text-center">
                      <a class="btn btn-link text-dark mb-0 px-2"
                         href="{{ route('admin.quote-levels.edit', $level) }}"
                         title="Modifica">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                      </a>

                      <button
                        type="button"
                        class="btn btn-link text-danger mb-0 px-2"
                        title="Elimina"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmDeleteModal"
                        data-action="{{ route('admin.quote-levels.destroy', $level) }}"
                        data-title="Eliminare livello?"
                        data-body="Vuoi eliminare il livello '{{ $level->name }}'? Verranno rimosse anche le opzioni collegate (se impostato così)."
                      >
                        <i class="fa fa-trash" aria-hidden="true"></i>
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="py-4 text-center text-secondary">
                      Nessun livello creato.
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

</div>

{{-- MODAL (stesso stile skills) --}}
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
      const button = event.relatedTarget;
      if (!button) return;

      document.getElementById('confirmDeleteForm').action = button.getAttribute('data-action');
      document.getElementById('confirmDeleteTitle').innerText = button.getAttribute('data-title') || 'Conferma eliminazione';
      document.getElementById('confirmDeleteBody').innerText  = button.getAttribute('data-body')  || 'Sei sicuro?';
    });
  });
</script>
@endpush
