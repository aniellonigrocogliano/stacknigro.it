@extends('layouts.admin')

@section('title', 'Skills')

@section('content')
<div class="mb-4 row">
  <div class="mb-4 col-lg-10 col-md-12 mb-md-0">
    <div class="card">
      <div class="pb-0 card-header">
        <div class="row">
          <div class="col-lg-6 col-7">
            <h6>Skills</h6>
            <p class="mb-0 text-sm">
              <i class="fa fa-check text-info" aria-hidden="true"></i>
              <span class="font-weight-bold ms-1">{{ $skills->count() }}</span> inserite
            </p>
          </div>

          <div class="my-auto col-lg-6 col-5 text-end">
            <a href="{{ route('admin.skills.create') }}" class="mb-0 btn bg-gradient-dark">
              <i class="fa fa-plus me-1" aria-hidden="true"></i> Aggiungi skill
            </a>
          </div>
        </div>
      </div>

      <div class="px-0 pb-2 card-body">
        <div class="table-responsive">
          <table class="table mb-0 align-items-center">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Skill</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Icona</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Colore</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Descrizione</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azioni</th>
              </tr>
            </thead>

            <tbody>
              @forelse($skills as $skill)
                <tr>
                  <td>
                    <div class="px-2 py-1 d-flex">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $skill->name }}</h6>
                        <p class="mb-0 text-xs text-secondary">Sort: {{ $skill->sort }}</p>
                      </div>
                    </div>
                  </td>

                  <td class="align-middle">
                    @if($skill->fa_icon)
                      <i class="{{ $skill->fa_icon }} text-dark"></i>
                      <span class="text-xs text-secondary ms-2">{{ $skill->fa_icon }}</span>
                    @else
                      <span class="text-xs text-secondary">—</span>
                    @endif
                  </td>

                  <td class="align-middle">
                    @if($skill->color)
                      <span class="badge" style="background: {{ $skill->color }}; color: #fff;">
                        {{ $skill->color }}
                      </span>
                    @else
                      <span class="text-xs text-secondary">—</span>
                    @endif
                  </td>

                  <td class="align-middle">
                    <span class="text-sm text-secondary">
                      {{ \Illuminate\Support\Str::limit(strip_tags($skill->description), 80) }}
                    </span>
                  </td>

                  <td class="text-center align-middle">
                    <a class="px-2 mb-0 btn btn-link text-dark"
                       href="{{ route('admin.skills.edit', $skill) }}"
                       title="Modifica">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>

                    {{-- DELETE trigger: apre modal --}}
                    <button
                      type="button"
                      class="px-2 mb-0 btn btn-link text-danger"
                      title="Elimina"
                      data-bs-toggle="modal"
                      data-bs-target="#confirmDeleteModal"
                      data-action="{{ route('admin.skills.destroy', $skill) }}"
                      data-title="Eliminare skill?"
                      data-body="Vuoi eliminare la skill '{{ $skill->name }}'? Questa azione non può essere annullata."
                    >
                      <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-4 text-center text-secondary">
                    Nessuna skill inserita.
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

{{-- MODAL CONFERMA ELIMINAZIONE (TEMPLATE) --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-normal" id="confirmDeleteTitle">Conferma eliminazione</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirmDeleteBody">
        Sei sicuro di voler eliminare questo elemento?
      </div>
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

      const action = button.getAttribute('data-action');
      const title  = button.getAttribute('data-title') || 'Conferma eliminazione';
      const body   = button.getAttribute('data-body')  || 'Sei sicuro?';

      const form = document.getElementById('confirmDeleteForm');
      form.action = action;

      document.getElementById('confirmDeleteTitle').innerText = title;
      document.getElementById('confirmDeleteBody').innerText  = body;
    });
  });
</script>
@endpush

