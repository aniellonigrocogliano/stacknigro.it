@extends('layouts.admin')

@section('title', 'Messaggio')

@section('content')
<div class="py-3 container-fluid">

  <div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-1">
        <i class="fa-regular fa-message me-2"></i>Messaggio
      </h5>
      <div class="text-sm text-muted">
        {{ $conversation->subject ?: '—' }}
      </div>
    </div>

    <div class="gap-2 d-flex">
      <a href="{{ route('inbox.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-inbox me-1"></i> Inbox
      </a>
      <a href="{{ route('inbox.archive') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-box-archive me-1"></i> Archivio
      </a>
      <a href="{{ route('inbox.trash') }}" class="btn btn-outline-danger btn-sm">
        <i class="fa-solid fa-trash me-1"></i> Cestino
      </a>
    </div>
  </div>

  {{-- AZIONI --}}
  <div class="mb-3 card">
    <div class="flex-wrap gap-2 card-body d-flex justify-content-between align-items-center">

      <div class="flex-wrap gap-2 d-flex">

        {{-- Letto / non letto (solo se non è nel cestino) --}}
        @if(!$conversation->trashed())
          @if($conversation->read_at)
            <form id="f-unread" method="POST" action="{{ route('inbox.unread', $conversation) }}">
              @csrf @method('PATCH')
              <button
                type="button"
                class="btn btn-outline-secondary btn-sm js-confirm"
                data-title="Conferma"
                data-body="Vuoi segnare questo messaggio come NON letto?"
                data-form="f-unread"
              >
                <i class="fa-regular fa-envelope me-1"></i> Non letto
              </button>
            </form>
          @else
            <form id="f-read" method="POST" action="{{ route('inbox.read', $conversation) }}">
              @csrf @method('PATCH')
              <button
                type="button"
                class="btn btn-outline-secondary btn-sm js-confirm"
                data-title="Conferma"
                data-body="Vuoi segnare questo messaggio come letto?"
                data-form="f-read"
              >
                <i class="fa-regular fa-envelope-open me-1"></i> Segna letto
              </button>
            </form>
          @endif
        @endif

        {{-- Archivio / Unarchive (solo se non è nel cestino) --}}
        @if(!$conversation->trashed())
          @if($conversation->archived_at)
            <form id="f-unarchive" method="POST" action="{{ route('inbox.unarchiveOne', $conversation) }}">
              @csrf @method('PATCH')
              <button
                type="button"
                class="btn btn-outline-secondary btn-sm js-confirm"
                data-title="Conferma"
                data-body="Vuoi ripristinare questo messaggio in Inbox?"
                data-form="f-unarchive"
              >
                <i class="fa-solid fa-box-open me-1"></i> Ripristina in Inbox
              </button>
            </form>
          @else
            <form id="f-archive" method="POST" action="{{ route('inbox.archiveOne', $conversation) }}">
              @csrf @method('PATCH')
              <button
                type="button"
                class="btn btn-outline-secondary btn-sm js-confirm"
                data-title="Conferma"
                data-body="Vuoi archiviare questo messaggio?"
                data-form="f-archive"
              >
                <i class="fa-solid fa-box-archive me-1"></i> Archivia
              </button>
            </form>
          @endif

          {{-- Cestina --}}
          <form id="f-trash" method="POST" action="{{ route('inbox.trashOne', $conversation) }}">
            @csrf @method('DELETE')
            <button
              type="button"
              class="btn btn-outline-danger btn-sm js-confirm"
              data-title="Conferma"
              data-body="Vuoi spostare questo messaggio nel cestino?"
              data-form="f-trash"
            >
              <i class="fa-solid fa-trash me-1"></i> Cestino
            </button>
          </form>
        @endif

        {{-- Se è nel cestino: ripristina + elimina definitivo --}}
        @if($conversation->trashed())
          <form id="f-restore" method="POST" action="{{ route('inbox.restore', $conversation->id) }}">
            @csrf @method('PATCH')
            <button
              type="button"
              class="btn btn-outline-secondary btn-sm js-confirm"
              data-title="Conferma"
              data-body="Vuoi ripristinare questo messaggio (torna in Inbox)?"
              data-form="f-restore"
            >
              <i class="fa-solid fa-rotate-left me-1"></i> Ripristina
            </button>
          </form>

          <form id="f-force" method="POST" action="{{ route('inbox.forceDelete', $conversation->id) }}">
            @csrf @method('DELETE')
            <button
              type="button"
              class="btn btn-danger btn-sm js-confirm"
              data-title="Eliminazione definitiva"
              data-body="Eliminare definitivamente questo messaggio? Azione irreversibile."
              data-form="f-force"
            >
              <i class="fa-solid fa-trash-can me-1"></i> Elimina definitivo
            </button>
          </form>
        @endif

      </div>

      <div class="text-sm text-muted">
        <i class="fa-regular fa-clock me-1"></i>
        Ricevuto: {{ optional($conversation->created_at)->format('d/m/Y H:i') }}
        @if($conversation->read_at)
          · Letto: {{ $conversation->read_at->format('d/m/Y H:i') }}
        @endif
        @if($conversation->replied_at)
          · Risposto: {{ $conversation->replied_at->format('d/m/Y H:i') }}
        @endif
      </div>

    </div>
  </div>

  <div class="row">
    {{-- DETTAGLI --}}
    <div class="mb-3 col-12 col-lg-5">
      <div class="card h-100">
        <div class="card-body">

          <h6 class="mb-3"><i class="fa-solid fa-user me-2"></i>Dati utente</h6>

          <div class="mb-2">
            <div class="text-xs text-muted">Nome</div>
            <div class="text-sm">{{ $conversation->name }}</div>
          </div>

          <div class="mb-2">
            <div class="text-xs text-muted">Email</div>
            <div class="text-sm">
              <a href="mailto:{{ $conversation->email }}">{{ $conversation->email }}</a>
            </div>
          </div>

          @if($conversation->phone)
            <div class="mb-2">
              <div class="text-xs text-muted">Telefono</div>
              <div class="text-sm">{{ $conversation->phone }}</div>
            </div>
          @endif

          <div class="mb-3">
            <div class="text-xs text-muted">Sorgente</div>
            <div class="text-sm">
              @if($conversation->source === 'quote')
                <i class="fa-solid fa-calculator me-1"></i>Preventivo
              @else
                <i class="fa-solid fa-envelope me-1"></i>Contatto
              @endif
            </div>
          </div>

          <hr class="horizontal dark">

          <h6 class="mb-3"><i class="fa-solid fa-shield-halved me-2"></i>Meta / Privacy</h6>

          <div class="mb-2">
            <div class="text-xs text-muted">Privacy accettata</div>
            <div class="text-sm">
              @if($conversation->privacy_accepted)
                <i class="fa-solid fa-check text-success me-1"></i> Sì
                @if($conversation->privacy_accepted_at)
                  <span class="text-muted">({{ $conversation->privacy_accepted_at->format('d/m/Y H:i') }})</span>
                @endif
              @else
                <i class="fa-solid fa-xmark text-danger me-1"></i> No
              @endif
            </div>
          </div>

          @if($conversation->how_found)
            <div class="mb-2">
              <div class="text-xs text-muted">Come ti ha trovato</div>
              <div class="text-sm">{{ $conversation->how_found }}</div>
            </div>
          @endif

          <div class="mb-2">
            <div class="text-xs text-muted">IP</div>
            <div class="text-sm font-monospace">{{ $conversation->ip_address ?: '—' }}</div>
          </div>

          <div class="mb-0">
            <div class="text-xs text-muted">User Agent</div>
            <div class="text-sm font-monospace" style="word-break: break-word;">
              {{ $conversation->user_agent ?: '—' }}
            </div>
          </div>

        </div>
      </div>
    </div>

    {{-- MESSAGGIO + PAYLOAD --}}
    <div class="mb-3 col-12 col-lg-7">
      <div class="mb-3 card">
        <div class="card-body">
          <h6 class="mb-3"><i class="fa-regular fa-pen-to-square me-2"></i>Messaggio</h6>
          <div class="text-sm" style="white-space: pre-wrap;">{{ $conversation->user_message }}</div>
        </div>
      </div>

      @if($conversation->source === 'quote' && !empty($conversation->quote_payload))
        <div class="card">
          <div class="card-body">
            <h6 class="mb-3"><i class="fa-solid fa-code me-2"></i>Payload preventivo (JSON)</h6>
            <pre class="p-3 mb-0 bg-gray-100 border-radius-lg"
                 style="max-height: 320px; overflow:auto;">{{ json_encode($conversation->quote_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- RISPOSTA (solo se NON nel cestino) --}}
  @if(!$conversation->trashed())
    <div class="card">
      <div class="card-body">
        <h6 class="mb-3"><i class="fa-solid fa-reply me-2"></i>Rispondi via email</h6>

        <form id="f-reply" method="POST" action="{{ route('inbox.reply', $conversation) }}">
          @csrf

          <div class="row g-2">
            <div class="col-12 col-lg-5">
              <label class="form-label">Oggetto</label>
              <input
                type="text"
                name="reply_subject"
                class="form-control @error('reply_subject') is-invalid @enderror"
                value="{{ old('reply_subject', $conversation->reply_subject ?: ('Re: ' . ($conversation->subject ?: 'Messaggio dal sito'))) }}"
              >
              @error('reply_subject')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-lg-7">
              <label class="form-label">Destinatario</label>
              <input type="text" class="form-control" value="{{ $conversation->email }}" disabled>
            </div>

            <div class="col-12">
              <label class="form-label">Testo risposta</label>
              <textarea
                name="reply_body"
                rows="6"
                class="form-control @error('reply_body') is-invalid @enderror"
                placeholder="Scrivi la risposta..."
              >{{ old('reply_body', $conversation->reply_body) }}</textarea>
              @error('reply_body')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="gap-2 mt-2 col-12 d-flex justify-content-end">
              <button
                type="button"
                class="btn btn-primary js-confirm"
                data-title="Invio risposta"
                data-body="Confermi l'invio della risposta via email?
                data-form="f-reply"
              >
                <i class="fa-solid fa-paper-plane me-1"></i> Invia risposta
              </button>
            </div>
          </div>
        </form>

        <hr class="my-4 horizontal dark">

        <div class="mb-2 text-xs text-muted">
          <i class="fa-regular fa-quote-left me-1"></i> Citazione automatica (sarà inclusa nella mail)
        </div>

        <blockquote class="p-3 mb-0 bg-gray-100 border-radius-lg" style="white-space: pre-wrap;">
{{ $conversation->user_message }}
        </blockquote>

      </div>
    </div>
  @endif

  {{-- MODAL CONFERMA AZIONE --}}
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
        const t = btn.getAttribute('data-title') || 'Conferma';
        const b = btn.getAttribute('data-body') || 'Sei sicuro?';

        titleEl.textContent = t;
        bodyEl.textContent = b;

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
