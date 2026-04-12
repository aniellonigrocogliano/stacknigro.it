@extends('layouts.admin')

@section('title', 'Inbox')

@section('content')
<div class="py-3 container-fluid">

  <div class="mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
      <i class="fa-solid fa-inbox me-2"></i>Inbox
    </h5>

    <div class="gap-2 d-flex">
      <a href="{{ route('inbox.archive') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-box-archive me-1"></i> Archivio
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
              class="{{ $c->read_at ? '' : 'fw-bold bg-gray-100' }}"
              style="cursor:pointer"
              onclick="window.location='{{ route('inbox.show', $c->id) }}'"
            >
              <td class="text-sm">
                {{ $c->name }}<br>
                <span class="text-xs text-muted">{{ $c->email }}</span>
              </td>

              <td class="text-sm">
                {{ $c->subject ?: '—' }}
                @if($c->is_unread)
                  <i class="fa-solid fa-circle text-primary ms-2" style="font-size:6px"></i>
                @endif
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
                {{ $c->created_at->format('d/m/Y H:i') }}
              </td>

              <td class="text-end" onclick="event.stopPropagation()">
                <div class="gap-2 d-flex justify-content-end">

                  {{-- letto / non letto --}}
                  @if($c->read_at)
                    <form method="POST" action="{{ route('inbox.unread', $c) }}">
                      @csrf @method('PATCH')
                      <button class="p-0 btn btn-link text-secondary" title="Segna come non letto">
                        <i class="fa-regular fa-envelope"></i>
                      </button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('inbox.read', $c) }}">
                      @csrf @method('PATCH')
                      <button class="p-0 btn btn-link text-success" title="Segna come letto">
                        <i class="fa-regular fa-envelope-open"></i>
                      </button>
                    </form>
                  @endif

                  {{-- archivia --}}
                  <form method="POST" action="{{ route('inbox.archiveOne', $c) }}">
                    @csrf @method('PATCH')
                    <button class="p-0 btn btn-link text-info" title="Archivia">
                      <i class="fa-solid fa-box-archive"></i>
                    </button>
                  </form>

                  {{-- cestino --}}
                  <form method="POST" action="{{ route('inbox.trashOne', $c) }}">
                    @csrf @method('DELETE')
                    <button class="p-0 btn btn-link text-danger" title="Cestino">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-4 text-center text-muted">
                Inbox vuota
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

