@extends('layouts.public')

@section('title', $project->title)

@section('content')

{{-- ===== TITOLO PROGETTO ===== --}}
<section class="section">
  <div class="container" data-aos="fade-up">
    <div class="flex-wrap gap-3 d-flex align-items-center justify-content-between section-title">

      {{-- Titolo --}}
      <div>
        <h2>Progetto</h2>
        <div>
          <span>{{ $project->title }}</span>
        </div>
      </div>

      {{-- Bottone ritorno --}}
      <a href="{{ route('public.projects.index') }}"
         class="btn btn-danger">
        <i class="fa-solid fa-arrow-left me-2"></i>
        Tutti i progetti
      </a>

    </div>
  </div>
</section>

{{-- ===== GALLERY ===== --}}
<section id="gallery" class="gallery section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-0">

      @foreach($project->images as $image)
        <div class="col-lg-3 col-md-4">
          <div class="gallery-item">
            <a
              href="{{ asset('storage/'.$image->path) }}"
              class="glightbox"
              data-gallery="project-gallery"
            >
              <img
                src="{{ asset('storage/'.$image->path) }}"
                alt="{{ $project->title }}"
                class="img-fluid"
              >
            </a>
          </div>
        </div>
      @endforeach

    </div>
  </div>
</section>

{{-- ===== DESCRIZIONE LUNGA ===== --}}
<section class="section">
  <div class="container" data-aos="fade-up">
    <div class="content">
      {!! $project->body !!}
    </div>
  </div>
</section>

@endsection
