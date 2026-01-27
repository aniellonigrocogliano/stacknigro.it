@extends('layouts.public')

@section('title', 'Progetti')

@section('content')
<section id="projects" class="team section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Progetti</h2>
    <div><span>Dai un'occhiata ai</span> <span class="description-title">Progetti</span></div>
  </div>

  <div class="container">
    <div class="row gy-5">

      @foreach($projects as $project)
        @php
          // cover: prima quella is_cover=1, altrimenti la prima disponibile
          $cover = $project->images->firstWhere('is_cover', 1) ?? $project->images->first();
          $coverUrl = $cover ? asset('storage/'.$cover->path) : asset('img/placeholder-project.jpg');
        @endphp

        <div class="col-lg-4 col-md-6" data-aos="fade-up">
          <a href="{{ route('public.projects.show', $project) }}" class="member d-block text-decoration-none">
            <div class="pic">
              <img src="{{ $coverUrl }}" class="img-fluid w-100" alt="{{ $project->title }}">
            </div>

            <div class="mt-3 member-info">
              <h4 class="text-dark">{{ $project->title }}</h4>
              <span class="text-secondary">
                {{ $project->excerpt }}
              </span>
            </div>
          </a>
        </div>
      @endforeach

    </div>
  </div>

</section>

@endsection
