@extends('layouts.public')
@section('content')
<div class="container py-5">

{{-- Bio --}}
@php
  $bioHtml = $site?->bio ?? '';
  $parts = explode('<!--more-->', $bioHtml, 2);
  $preview = trim($parts[0] ?? '');
  $hasMore = isset($parts[1]);
@endphp

@if($preview)
  {!! $preview !!}
  @if($hasMore)
    <a href="{{ route('bio') }}">Continua a leggere</a>
  @endif
@endif
</div>
@endsection
{{--    Fine Bio --}}
