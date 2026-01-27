@extends('layouts.public')

@section('title', 'Contatti')

@section('content')
<section id="contact" class="contact section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Contatti</h2>
    <div><span>Scrivimi per</span> <span class="description-title">informazioni</span></div>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
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
@endsection
