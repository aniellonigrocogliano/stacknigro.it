@extends('layouts.admin')

@section('title', 'Messaggio')

@section('content')
<div class="py-4 container-fluid">

  <div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
      <h4 class="mb-0">Messaggio</h4>
      <p class="mb-0 text-sm text-secondary">
        {{ $conversation->source === 'quote' ? 'Preventivo' : 'Contatto' }}
        — ricevuto il {{ $conversation->created_at->format('d/m/Y H:i') }}
      </p>
    </div>

    <div class="gap-2 d-flex">
      {{-- torna indietro --}}
      <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Indietro
      </a>

      {{-- archivia / ripristina --}}
      @if(is_null($conversation->archived_at))
        <form method="POST" action="{{ route('admin.inbox.doArchive', $conversation) }}">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-outline-secondary">
            <i class="fa-solid fa-box-archive me-1"></i> Archivia
          </button>
        </form>
      @else
        <form method="POST" action="{{ route('admin.inbox.unarchive', $conversation) }}">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-rotate-left me-1"></i> Ripristina
          </button>
        </form>
      @endif

      {{-- cestino --}}
      <form method="POST" action="{{ route('admin.inbox.destroy', $conversation) }}">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-outline-danger"
                onclick="return confirm('Spostare nel cestino questo messaggio?')">
          <i class="fa-solid fa-trash me-1"></i> Cestino
        </button>
      </form>
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

  <div class="row">
    {{-- COLONNA SINISTRA: Dettagli --}}
    <div class="col-12 col-lg-5">
      <div class="mb-4 card">
        <div class="pb-0 card-header">
          <h6 class="mb-0">Dati utente</h6>
        </div>
        <div class="card-body">
          <div class="mb-2">
            <div class="text-xs text-secondary">Nome</div>
            <div class="text-sm fw-bold">{{ $conversation->name }}</div>
          </div>

          <div class="mb-2">
            <div class="text-xs text-secondary">Email</div>
            <div class="text-sm fw-bold">{{ $conversation->email }}</div>
          </div>

          <div class="mb-2">
            <div class="text-xs text-secondary">Telefono</div>
            <div class="text-sm">{{ $conversation->phone ?: '—' }}</div>
          </div>

          <div class="mb-2">
            <div class="text-xs text-secondary">Come mi hai trovato</div>
            <div class="text-sm">{{ $conversation->how_found ?: '—' }}</div>
          </div>

          <div class="mb-2">
            <div class="text-xs text-secondary">Privacy</div>
            <div class="text-sm">
              @if($conversation->privacy_accepted)
                <span class="badge bg-success">Accettata</span>
                <span class="text-xs text-secondary ms-1">
                  {{ optional($conversation->privacy_accepted_at)->format('d/m/Y H:i') }}
                </span>
              @else
                <span class="badge bg-danger">NO</span>
              @endif
            </div>
          </div>

          <hr>

          <div class="mb-2">
            <div class="text-xs text-secondary">IP</div>
            <div class="text-sm">{{ $conversation->ip_address ?: '—' }}</div>
          </div>

          <div class="mb-0">
            <div class="text-xs text-secondary">User Agent</div>
            <div class="text-sm text-break">{{ $conversation->user_agent ?: '—' }}</div>
          </div>
        </div>
      </div>

      {{-- Payload preventivo --}}
      @if($conversation->source === 'quote' && !empty($conversation->quote_payload))
        <div class="card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Payload preventivo</h6>
            <p class="mb-0 text-sm text-secondary">Risultato calcolo salvato (JSON).</p>
          </div>
          <div class="card-body">
            <pre class="mb-0" style="white-space: pre-wrap;">{{ json_encode($conversation->quote_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
          </div>
        </div>
      @endif
    </div>

    {{-- COLONNA DESTRA: Messaggio + Reply --}}
    <div class="col-12 col-lg-7">
      <div class="mb-4 card">
        <div class="pb-0 card-header">
          <h6 class="mb-0">{{ $conversation->subject ?: 'Senza oggetto' }}</h6>
          <p class="mb-0 text-sm text-secondary">
            Stato:
            @if(is_null($conversation->read_at))
              <span class="badge bg-info">Non letto</span>
            @else
              <span class="badge bg-light text-dark">Letto</span>
            @endif

            @if(!is_null($conversation->replied_at))
              <span class="badge bg-success ms-1">Risposto</span>
            @endif
          </p>
        </div>
        <div class="card-body">
          <div class="p-3 border rounded-3">
            {!! nl2br(e($conversation->user_message)) !!}
          </div>
        </div>
      </div>

      {{-- Reply --}}
      <div class="card">
        <div class="pb-0 card-header">
          <h6 class="mb-0">Rispondi via email</h6>
          <p class="mb-0 text-sm text-secondary">
            La risposta verrà inviata da <strong>aniello@stacknigro.it</strong> e chiuderà la conversazione.
          </p>
        </div>

        <div class="card-body">
          @if(!is_null($conversation->replied_at))
            <div class="text-white alert alert-success" role="alert">
              <i class="fa-solid fa-circle-check me-1"></i>
              Hai già risposto il {{ $conversation->replied_at->format('d/m/Y H:i') }}. Conversazione chiusa.
            </div>

            <div class="mb-2 text-xs text-secondary">Oggetto inviato</div>
            <div class="mb-3 text-sm fw-bold">{{ $conversation->reply_subject }}</div>

            <div class="mb-2 text-xs text-secondary">Testo inviato</div>
            <div class="p-3 border rounded-3">
              {!! nl2br(e($conversation->reply_body)) !!}
            </div>
          @else
            <form method="POST" action="{{ route('admin.inbox.reply', $conversation) }}">
              @csrf

              <div class="mb-3">
                <label class="form-label">Oggetto email</label>
                <input type="text"
                       name="reply_subject"
                       class="form-control"
                       required
                       maxlength="180"
                       value="{{ old('reply_subject', 'Re: ' . ($conversation->subject ?: 'Messaggio')) }}">
              </div>

              <div class="mb-3">
                <label class="form-label">Risposta</label>
                <textarea name="reply_body" class="form-control" rows="7" required>{{ old('reply_body') }}</textarea>
                <div class="mt-1 text-xs text-secondary">
                  L’email includerà automaticamente la citazione del messaggio originale.
                </div>
              </div>

              <button type="submit" class="mb-0 btn btn-outline-success">
                <i class="fa-solid fa-paper-plane me-1"></i> Invia risposta
              </button>
            </form>
          @endif
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
