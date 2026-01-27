@extends('layouts.public')

@section('title', 'Skills')

@section('content')
<section id="skills" class="stats section light-background">
  <div class="container section-title" data-aos="fade-up">
    <h2>Skills</h2>
    <div><span>Le mie</span> <span class="description-title">competenze</span></div>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">

      @foreach($skills as $skill)
        <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center" data-aos="fade-up">
          {{-- ✅ ICONA FUORI dalla card (così prende il CSS "stats" e interseca) --}}
          <i class="{{ $skill->fa_icon }}" style="color: {{ $skill->color }};"></i>

          <div class="text-center stats-item">
            {{-- al posto del numero --}}
            <span class="d-block fw-bold" style="font-size: 1.2rem;">
              {{ $skill->name }}
            </span>

            {{-- descrizione --}}
            <p class="mb-0">{{ $skill->description }}</p>
          </div>
        </div>
      @endforeach

    </div>
  </div>
</section>

@endsection
