<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', 'Admin')</title>

  <link href="{{ asset('themes/admin/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('themes/admin/css/nucleo-svg.css') }}" rel="stylesheet" />
  <link href="{{ asset('themes/admin/css/material-dashboard.min.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
  <link rel="icon" type="image/png"
        href="{{ $site?->favicon_path ? asset('storage/'.$site->favicon_path) : asset('themes/bootslander/img/favicon.png') }}">
</head>

<body class="bg-gray-200 g-sidenav-show">

<div class="px-3 d-xl-none" style="padding-top:10px; padding-bottom:6px;">
  <div class="d-flex justify-content-end">
    <a href="javascript:;" id="iconNavbarSidenav" class="text-body" aria-label="Apri menu">
      <i class="fa-solid fa-bars fa-lg"></i>
    </a>
  </div>
</div>

  {{-- SIDEBAR --}}
  @include('admin.partials.sidebar')

  {{-- CONTENUTO --}}
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    @yield('content')
  </main>

  {{-- MODAL GLOBALE AVVISI (SUCCESS / ERROR) --}}
  <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-normal" id="alertModalTitle">Avviso</h5>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="alertModalBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">
            Chiudi
          </button>
        </div>
      </div>
    </div>
  </div>


<a href="javascript:;" id="scrollTopBtn"
   class="position-fixed d-flex align-items-center justify-content-center"
   style="
     right:20px;
     bottom:20px;
     width:48px;
     height:48px;
     background:#fff;
     border-radius:50%;
     box-shadow:0 4px 12px rgba(0,0,0,.2);
     z-index:1100;
     color:#344767;
     text-decoration:none;
   "
   aria-label="Torna su">
  <i class="fa-solid fa-arrow-up"></i>
</a>

  {{-- CORE JS (SOLO QUI, NON NEL LOGIN) --}}
  <script src="{{ asset('themes/admin/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/material-dashboard.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/plugins/chartjs.min.js') }}"></script>

  {{-- AUTO-OPEN MODAL DA SESSION --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const modalEl = document.getElementById('alertModal');
      if (!modalEl || typeof bootstrap === 'undefined') return;

      const modal = new bootstrap.Modal(modalEl);

      @if(session('success'))
        document.getElementById('alertModalTitle').innerText = 'Operazione completata';
        document.getElementById('alertModalBody').innerText = @json(session('success'));
        modal.show();
      @endif

      @if(session('error'))
        document.getElementById('alertModalTitle').innerText = 'Errore';
        document.getElementById('alertModalBody').innerText = @json(session('error'));
        modal.show();
      @endif
    });
  </script>

<script>
(() => {
  const btn = document.getElementById('scrollTopBtn');
  if (!btn) return;

  btn.addEventListener('click', (e) => {
    e.preventDefault();

    // scroll container (se esiste)
    const main = document.querySelector('.main-content');
    if (main) main.scrollTop = 0;

    // fallback universali
    document.documentElement.scrollTop = 0;
    document.body.scrollTop = 0;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();
</script>


  @stack('scripts')


</body>
</html>
