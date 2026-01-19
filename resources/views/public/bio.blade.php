@extends('layouts.public')

@section('title', 'Bio')

@section('content')
@php
  $bioFull = str_replace('<!--more-->', '', $site?->bio ?? '');
@endphp

<section class="section">
  <div class="container">
    @if(trim($bioFull))
      {!! $bioFull !!}
    @else
      <p>Bio non disponibile.</p>
    @endif
  </div>
</section>
@endsection
