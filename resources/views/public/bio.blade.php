@extends('layouts.public')

@section('title', 'Bio')

@section('content')
@php
  $bioFull = str_replace('<!--more-->', '', $site?->bio ?? '');
@endphp

<section class="section">
          <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Chi sono</h2>
        <div><span>Scopri di</span> <span class="description-title">Più</span></div>
      </div><!-- End Section Title -->
  <div class="container">
    @if(trim($bioFull))
      {!! $bioFull !!}
    @else
      <p>Bio non disponibile.</p>
    @endif
  </div>
</section>
@endsection
