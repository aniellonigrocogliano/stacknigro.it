@extends('layouts.public')

@section('title', 'Privacy & Cookie Policy')

@section('content')
<div class="container py-5">

  <h1 class="mb-4">Privacy & Cookie Policy</h1>

  {{-- NAV TABS (Bootstrap) --}}
  <div class="mb-4 nav-wrapper position-relative end-0">
    <ul class="p-1 nav nav-pills nav-fill" id="policyTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="px-0 py-1 mb-0 nav-link active"
           id="privacy-tab"
           data-bs-toggle="tab"
           href="#privacy"
           role="tab"
           aria-controls="privacy"
           aria-selected="true">
          <i class="mb-1 align-middle fa-solid fa-user-shield me-1"></i>
          Privacy
        </a>
      </li>

      <li class="nav-item" role="presentation">
        <a class="px-0 py-1 mb-0 nav-link"
           id="cookies-tab"
           data-bs-toggle="tab"
           href="#cookies"
           role="tab"
           aria-controls="cookies"
           aria-selected="false">
          <i class="mb-1 align-middle fa-solid fa-cookie-bite me-1"></i>
          Cookies
        </a>
      </li>
    </ul>
  </div>

  {{-- TAB CONTENT --}}
  <div class="tab-content" id="policyTabsContent">

    {{-- PRIVACY --}}
    <div class="tab-pane fade show active"
         id="privacy"
         role="tabpanel"
         aria-labelledby="privacy-tab">
      {{-- Ancora esterna --}}
      <a id="privacy-anchor"></a>

      @if(!empty($privacy?->content))
        {!! $privacy->content !!}
      @else
        <div class="alert alert-warning">
          La police privacy ancora non è stata pubblicata.
        </div>
      @endif
    </div>

    {{-- COOKIES --}}
    <div class="tab-pane fade"
         id="cookies"
         role="tabpanel"
         aria-labelledby="cookies-tab">
      {{-- Ancora esterna --}}
      <a id="cookies-anchor"></a>

      @if(!empty($cookies?->content))
        {!! $cookies->content !!}
      @else
        <div class="alert alert-warning">
          La police cookies ancora non è stata pubblicata.
        </div>
      @endif

      {{-- ✅ BOX PREFERENZE SOLO QUI --}}
      @include('public.partials.cookie-preferences-box')
    </div>

  </div>
</div>

{{-- Apri tab giusta se arrivi con #cookies o #privacy --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // accetta sia #cookies che #cookies-anchor
  const hash = (window.location.hash || '').toLowerCase();

  let target = null;
  if (hash === '#cookies' || hash === '#cookies-anchor') target = '#cookies';
  if (hash === '#privacy' || hash === '#privacy-anchor') target = '#privacy';

  if (!target) return;

  const triggerEl = document.querySelector(`a[href="${target}"]`);
  if (triggerEl && window.bootstrap?.Tab) {
    new bootstrap.Tab(triggerEl).show();
  }
});
</script>
@endpush

@endsection
