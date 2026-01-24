@extends('layouts.admin')

@section('title', 'Inbox')

@section('content')
<div class="py-4 container-fluid">

  <div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
      <h4 class="mb-0">Inbox</h4>
      <p class="mb-0 text-sm text-secondary">Messaggi da Contatto e Preventivi (stile Gmail).</p>
    </div>

    {{-- BOTTONI ALTO A DESTRA: Archivio + Cestino --}}
    <div class="btn-group">
      <a href="{{ route('admin.inbox.index') }}"
         class="btn btn-outline-secondary {{ $folder === 'inbox' ? 'active' : '' }}"
         title="Inbox">
        <i class="fa-solid fa-inbox me-1"></i> Inbox
      </a>
      <a href="{{ route('admin.inbox.archive') }}"
         class="btn btn-outline-secondary {{ $folder === 'archive' ? 'active' : '' }}"
         title="Archivio">
        <i class="fa-solid fa-box-archive me-1"></i> Archivio
      </a>
      <a href="{{ route('admin.inbox.trash') }}"
         class="btn btn-outline-secondary {{ $folder === 'trash' ? 'active' : '' }}"
         title="Cestino">
        <i class="fa-solid fa-trash me-1"></i> Cestino
      </a>
    </div>
  </div>

  {{-- FLASH --}}
  @if (session('success'))
    <div class="text-white alert alert-success" role="alert">
      <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
    </div>
  @endif
  @if (session('error'))
    <div class="text-white alert alert-danger" role="alert">
      <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="text-white alert alert-danger" role="alert">
      <div class="mb-1 fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Errori</div>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="pb-0 card-header">
      <div class="row align-items-center">
        <div class="col-12 col-lg-6">
          <h6 class="mb-0">
            {{ $folder === 'archive' ? 'Archivio' : ($folder === 'trash' ? 'Cestino' : 'Inbox') }}
          </h6>
          <p class="mb-0 text-sm text-secondary">
            @if($folder === 'trash')
              Qui ci sono i messaggi eliminati (soft delete).
            @elseif($folder === 'archive')
              Qui ci sono i messaggi archiviati.
            @else
              Qui ci sono i messaggi attivi.
            @endif
          </p>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table mb-0 align-items-center">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Da</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Oggetto</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stato</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data</th>
              <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azioni</th>
            </tr>
          </thead>

          <tbody>
            @forelse($items as $c)
              @php
                $isRead = !is_null($c->read_at);
                $isArchived = !is_null($c->archived_at);
                $isReplied = !is_null($c->replied_at);
                $typeLabel = $c->source === 'quote' ? 'Preventivo' : 'Contatto';
              @endphp

              <tr style="cursor:pointer"
                  class="{{ $isRead ? '' : 'fw-bold' }}"
                  onclick="window.location='{{ route('admin.inbox.show', $c) }}'">

                <td class="align-middle">
                  <div class="d-flex flex-column">
                    <span class="text-sm">{{ $c->name }}</span>
                    <span class="text-xs text-secondary fw-normal">{{ $c->email }}</span>
                  </div>
                </td>

                <td class="align-middle" style="min-width: 280px;">
                  <div class="d-flex flex-column">
                    <span class="text-sm">
                      {{ $c->subject ?: 'Senza oggetto' }}
                    </span>
                    <span class="text-xs text-secondary fw-normal text-truncate" style="max-width: 520px;">
                      {{ \Illuminate\Support\Str::limit($c->user_message, 90) }}
                    </span>
                  </div>
                </td>

                <td class="text-center align-middle">
                  <span class="badge bg-secondary">{{ $typeLabel }}</span>
                </td>

                <td class="text-center align-middle">
                  @if(!$isRead)
                    <span class="badge bg-info">Non letto</span>
                  @else
                    <span class="badge bg-light text-dark">Letto</span>
                  @endif

                  @if($isReplied)
                    <span class="badge bg-success ms-1">Risposto</span>
                  @endif

                  @if($isArchived && $folder !== 'archive')
                    <span class="badge bg-secondary ms-1">Archiviato</span>
                  @endif
                </td>

                <td class="text-center align-middle">
                  <span class="text-sm fw-normal">{{ $c->created_at->format('d/m/Y H:i') }}</span>
                </td>

                <td class="align-middle text-end" onclick="event.stopPropagation()">
                  @if($folder !== 'trash')
                    {{-- Letto/Non letto --}}
                    <form class="d-inline" method="POST" action="{{ route('admin.inbox.'.($isRead ? 'unread' : 'read'), $c) }}">
                      @csrf @method('PATCH')
                      <button type="submit" class="mb-0 btn btn-sm btn-outline-primary" title="{{ $isRead ? 'Segna come non letto' : 'Segna come letto' }}">
                        <i class="fa-solid {{ $isRead ? 'fa-envelope' : 'fa-envelope-open' }}"></i>
                      </button>
                    </form>

                    {{-- Archivio / Ripristina --}}
                    @if($folder === 'archive')
                      <form class="d-inline" method="POST" action="{{ route('admin.inbox.unarchive', $c) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="mb-0 btn btn-sm btn-outline-secondary" title="Ripristina in Inbox">
                          <i class="fa-solid fa-arrow-rotate-left"></i>
                        </button>
                      </form>
                    @else
                      <form class="d-inline" method="POST" action="{{ route('admin.inbox.doArchive', $c) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="mb-0 btn btn-sm btn-outline-secondary" title="Archivia">
                          <i class="fa-solid fa-box-archive"></i>
                        </button>
                      </form>
                    @endif

                    {{-- Cestino --}}
                    <form class="d-inline" method="POST" action="{{ route('admin.inbox.destroy', $c) }}">
                      @csrf @method('DELETE')
                      <button type="submit" class="mb-0 btn btn-sm btn-outline-danger" title="Sposta nel cestino"
                              onclick="return confirm('Spostare nel cestino questo messaggio?')">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </form>
                  @else
                    <span class="text-xs text-secondary">Nel cestino</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-4 text-center text-secondary">
                  Nessun messaggio.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $items->links() }}
      </div>
    </div>
  </div>

</div>
@endsection
