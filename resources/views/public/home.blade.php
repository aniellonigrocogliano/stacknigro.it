@extends('layouts.public')
@section('content')
<div class="container py-5">

{{-- Bio --}}
<section id="about" class="pt-2 section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Chi sono</h2>
    <div>
      <span>Scopri di</span>
      <span class="description-title">Più</span>
    </div>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    @if(trim(strip_tags($bioExcerpt)))
      {!! $bioExcerpt !!}

      @if($hasMore)
    …
    <a href="{{ url('/bio') }}">
      Scopri di più <i class="fa-solid fa-arrow-right"></i>
    </a>

      @endif
    @else
      <p>Bio non disponibile.</p>
    @endif
  </div>
</section>

{{--    Fine Bio --}}

{{-- Skills (HOME) --}}
<section id="skills" class="pt-2 features section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Skills</h2>
    <div><span>Le mie</span> <span class="description-title">competenze</span></div>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">

      @foreach($homeSkills as $i => $skill)
        <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="{{ 100 + ($i * 100) }}">
          <div class="features-item">
            <i class="{{ $skill->fa_icon ?? 'fa-solid fa-star' }}"
               style="color: {{ $skill->color ?? '#08005E' }}; font-size: 2.5rem;"></i>

            <h3 class="mb-0">
              <a href="{{ route('public.skills') }}" class="stretched-link">
                {{ $skill->name }}
              </a>
            </h3>
          </div>
        </div>
      @endforeach

    </div>
    <div class="mt-4">
  <span>Molto altro ancora</span>
  <a href="{{ route('public.skills') }}" class="ms-2 text-decoration-none">
    <span>nelle mie skills</span>
    <i class="fa-solid fa-arrow-right ms-1"></i>
  </a>
</div>
  </div>
</section>
{{-- /Skills (HOME) --}}

{{-- Progetti --}}
<section id="projects" class="team section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Progetti</h2>
    <div><span>Alcuni dei miei</span> <span class="description-title">progetti</span></div>
  </div>

  <div class="container">
    <div class="row gy-5">

      @forelse($homeProjects as $project)
        @php
          $cover = $project->images->first();
          $img = $cover ? asset('storage/'.$cover->path) : asset('themes/bootslander/img/placeholder.jpg');
        @endphp

        <div class="col-lg-4 col-md-6" data-aos="fade-up">
          <div class="member">
            <div class="pic">
              <a href="{{ route('public.projects.show', $project) }}">
                <img src="{{ $img }}" class="img-fluid" alt="{{ $project->title }}">
              </a>
            </div>

            <div class="mt-3 member-info">
              <h4>
                <a href="{{ route('public.projects.show', $project) }}" class="text-decoration-none">
                  {{ $project->title }}
                </a>
              </h4>
              <span>{{ $project->excerpt }}</span>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <p class="mb-0 text-center">Nessun progetto disponibile.</p>
        </div>
      @endforelse

    </div>
  </div>
<div class="mt-4 ">
  <span class="text-muted">
    Questi sono solo alcuni dei progetti che ho realizzato, nati da esigenze reali e trasformati in soluzioni concrete —
    <a href="{{ route('public.projects.index') }}" class="fw-semibold ">
      scoprili tutti →
    </a>
  </span>
</div>

{{-- Progetti end --}}

{{-- Preventivi --}}

<section id="quote-home" class="section ">
    <div class="container section-title" data-aos="fade-up">
    <h2>Ma quanto mi costa?</h2>
    <div><span>Calcola</span> <span class="description-title">il tuo preventivo</span></div>
  </div>
  <div class="container" data-aos="fade-up">

    <div class="row align-items-center gy-4">

      {{-- IMMAGINE --}}
      <div class="col-lg-6 d-flex justify-content-center">
        <img
          src="{{ asset('storage/site/preventivo-home.png') }}"
          alt="Richiedi un preventivo"
          class="rounded img-fluid"
          style="max-height: 450px;"
        >
      </div>

      {{-- TESTO --}}
      <div class="col-lg-6">

        <div class="mb-3 d-flex align-items-start">
          <i class="fa-regular fa-lightbulb fa-2x me-3 text-warning"></i>
          <div>
            <h3 class="mb-2">Serve una soluzione, non solo un’idea</h3>
            <p class="mb-0">
              <strong>Aniello Nigro Cogliano</strong> è qui per trasformare problemi
              complessi in soluzioni concrete.<br>
              Raccontami cosa ti serve e costruiamo qualcosa che funzioni davvero.
            </p>
          </div>
        </div>
<div class="mt-4 text-end">
        <a href="{{ route('public.quotes') }}"
           class="btn btn-success d-inline-flex align-items-center">
          Richiedi un preventivo
          <i class="fa-solid fa-arrow-right ms-2"></i>
        </a>
</div>
<p class="mt-2 text-muted fst-italic text-end small">
  Puoi creare il tuo preventivo in autonomia, senza impegni.
</p>
      </div>

    </div>

  </div>
</section>


      {{-- CONTATTI --}}
<section id="contact" class="contact section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Contatti</h2>
    <div><span>Scrivimi per</span> <span class="description-title">informazioni</span></div>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">

      {{-- CONTATTI --}}
      <div class="col-lg-4">
        @include('public.partials.contacts-info')
      </div>

      {{-- FORM --}}
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


{{-- Form --}}
@endsection

