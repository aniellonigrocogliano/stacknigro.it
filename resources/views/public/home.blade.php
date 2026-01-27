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

