@extends('layouts.admin')

@section('title', 'Progetti')

@section('content')
<div class="mb-4 row">
  <div class="mb-4 col-12">
    <div class="card">

      <div class="pb-0 card-header">
        <div class="row align-items-center">
          <div class="col-lg-6 col-7">
            <h6>Progetti</h6>
            <p class="mb-0 text-sm">
              <i class="fa fa-folder-open text-info" aria-hidden="true"></i>
              <span class="font-weight-bold ms-1">{{ $projects->count() }}</span> totali
            </p>
          </div>

          <div class="my-auto col-lg-6 col-5 text-end">
            <a href="{{ route('admin.projects.create') }}" class="mb-0 btn bg-gradient-dark">
              <i class="fa fa-plus me-1" aria-hidden="true"></i> Aggiungi progetto
            </a>
          </div>
        </div>
      </div>

      <div class="px-0 pb-2 card-body">
        <div class="table-responsive">
          <table class="table mb-0 align-items-center">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Progetto</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pubblicato</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Immagini</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azioni</th>
              </tr>
            </thead>

            <tbody>
              @forelse($projects as $project)
                <tr>
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

                  <td class="text-center align-middle">
                    <a href="{{ route('admin.projects.edit', $project) }}"
                       class="px-2 mb-0 btn btn-link text-dark"
                       title="Modifica">
                      <i class="fa fa-pen"></i>
                    </a>

                    {{-- DELETE trigger (modal) --}}
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
                  <td colspan="4" class="py-4 text-center text-secondary">
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
    const modalEl = document.getElementById('confirmDeleteModal');
    if (!modalEl) return;

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
  });
</script>
@endpush
