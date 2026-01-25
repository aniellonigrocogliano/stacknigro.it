@extends('layouts.admin')

@section('title', 'Archivio')

@section('content')
<div class="py-3 container-fluid">

  <div class="mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
      <i class="fa-solid fa-box-archive me-2"></i>Archivio
    </h5>

    <div class="gap-2 d-flex">
      <a href="{{ route('inbox.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-inbox me-1"></i> Inbox
      </a>
      <a href="{{ route('inbox.trash') }}" class="btn btn-outline-danger btn-sm">
        <i class="fa-solid fa-trash me-1"></i> Cestino
      </a>
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
              <th class="text-end">Data</th>
              <th class="text-end">Azioni</th>
            </tr>
          </thead>
          <tbody>

          @forelse($conversations as $c)
            <tr
              style="cursor:pointer"
              onclick="window.location='{{ route('inbox.show', $c->id) }}'"
            >
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
                {{ $c->created_at->format('d/m/Y H:i') }}
              </td>

              <td class="text-end" onclick="event.stopPropagation()">
                <div class="gap-3 d-flex justify-content-end">

                  {{-- Ripristina da archivio --}}
                  <form method="POST" action="{{ route('inbox.unarchiveOne', $c) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="p-0 btn btn-link" title="Ripristina in Inbox">
                      <i class="fa-solid fa-box-open"></i>
                    </button>
                  </form>

                  {{-- Cestina --}}
                  <form method="POST" action="{{ route('inbox.trashOne', $c) }}"
                        onsubmit="return confirm('Spostare questo messaggio nel cestino?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-0 btn btn-link text-danger" title="Cestino">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-4 text-center text-muted">
                Nessun messaggio archiviato
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
