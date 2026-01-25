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
      <form method="POST" action="{{ route('inbox.trash.empty') }}"
            onsubmit="return confirm('Vuoi svuotare il cestino? Questa azione elimina definitivamente tutti i messaggi nel cestino.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">
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
                  <form method="POST" action="{{ route('inbox.restore', $c->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="p-0 btn btn-link" title="Ripristina">
                      <i class="fa-solid fa-rotate-left"></i>
                    </button>
                  </form>

                  {{-- Elimina definitivo --}}
                  <form method="POST" action="{{ route('inbox.forceDelete', $c->id) }}"
                        onsubmit="return confirm('Eliminare definitivamente questo messaggio? Azione irreversibile.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-0 btn btn-link text-danger" title="Elimina definitivamente">
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

</div>
@endsection
