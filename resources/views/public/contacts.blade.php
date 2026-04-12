@extends('layouts.public')

@section('title', 'Contatti')

@section('content')
<section id="contact" class="contact section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Contatti</h2>
    <div><span>Scrivimi per</span> <span class="description-title">informazioni</span></div>
  </div><div class="container" data-aos="fade-up" data-aos-delay="100">

    @if ($errors->any())
      <div class="alert alert-danger mb-4">
        <ul class="mb-0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="row gy-4">

      {{-- CONTATTI (colonna sinistra) --}}
      <div class="col-lg-4">
        @include('public.partials.contacts-info', [
          'contacts' => $contacts ?? []
        ])
      </div>

      {{-- FORM (colonna destra) --}}
      <div class="col-lg-8">
        @include('public.partials.contact-form', [
          'action' => route('public.contacts.store'),
          'source' => 'contact',
          'quotePayload' => null,
        ])
      </div>

    </div>
  </div>
</section>

{{-- MODALE DI SUCCESSO --}}
<div class="modal fade" id="contactSuccessModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
      <div class="p-5 text-center modal-body">
        <div class="mb-3">
            <i class="bi bi-check-circle text-success" style="font-size: 3.5rem;"></i>
        </div>
        <h3 class="fw-bold">Messaggio Inviato!</h3>
        <p id="successMessage" class="mb-4 text-muted fs-5">
            {{ session('success') }}
        </p>
        <button type="button" class="px-5 py-2 btn btn-primary rounded-pill fw-bold" data-bs-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Se esiste un messaggio di successo in sessione, mostra la modale
    @if (session('success'))
        const successModal = new bootstrap.Modal(document.getElementById('contactSuccessModal'));
        successModal.show();
    @endif
});
</script>
@endpush
