@php
  $title = $site?->hero_title ?? 'Fullstacknigro.it - Aniello Nigro Cogliaano';
  $subtitle = $site?->hero_subtitle ?? 'Full Stack Developer (Junior)';
@endphp

<section id="hero" class="hero section dark-background"
  @if(!($showContent ?? false))
    style="min-height: 220px; padding: 120px 0 60px;"
  @endif
>
  {{-- SFONDO: puoi decidere se tenerlo statico o farlo dinamico più avanti --}}
  <img src="{{ asset('themes/bootslander/img/hero-bg-2.jpg') }}" alt="" class="hero-bg">

  <div class="container">
    @if($showContent ?? false)
      <div class="row gy-4 justify-content-between">

        <div class="col-lg-4 order-lg-last hero-img" data-aos="zoom-out" data-aos-delay="100">
          {{-- qui metto il logo dinamico (quello che carichi dal pannello) --}}
          <img
  src="{{ $site?->logo_path
    ? asset('storage/'.$site->logo_path).'?v='.($site->updated_at?->timestamp ?? time())
    : asset('themes/bootslander/img/hero-img.png') }}"
  class="img-fluid animated"
  alt="hero logo"
>
        </div>

@if(!empty($site?->hero_title))
  @php
    // Split sul primo "-"
    [$before, $after] = array_pad(explode('-', $site->hero_title, 2), 2, null);
  @endphp

  <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-in">
    <h1>
      {{ trim($before) }}
      @if($after)
        - <span>{{ trim($after) }}</span>
      @endif
    </h1>

    @if(!empty($site->hero_subtitle))
      <p>{{ $site->hero_subtitle }}</p>
    @endif
  </div>
@endif

      </div>
    @endif
  </div>

  {{-- ONDE: 1:1 dal template --}}
  <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg"
       xmlns:xlink="http://www.w3.org/1999/xlink"
       viewBox="0 24 150 28" preserveAspectRatio="none">
    <defs>
      <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
    </defs>
    <g class="wave1">
      <use xlink:href="#wave-path" x="50" y="3"></use>
    </g>
    <g class="wave2">
      <use xlink:href="#wave-path" x="50" y="0"></use>
    </g>
    <g class="wave3">
      <use xlink:href="#wave-path" x="50" y="9"></use>
    </g>
  </svg>
</section>


