<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Errore 403</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('frontend/assets/css/main.css') }}" rel="stylesheet">
  @php
    $favicon = \App\Models\HeroSetting::first()?->favicon;
@endphp
@if($favicon)
    <link rel="icon" type="image/png" href="{{ asset($favicon) }}">
@endif
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
<a href="{{ route('homepage') }}" class="logo d-flex align-items-center">
  @if($hero && $hero->logo)
    <img src="{{ asset($hero->logo) }}" alt="Logo" style="max-height: 40px;">
  @else
    <h1 class="sitename">Stacknigro</h1>
  @endif
</a>


      <nav id="navmenu" class="navmenu">
 <ul>
  <li><a href="{{ url('/#hero') }}" class="active">Home</a></li>
  <li><a href="{{ url('/#about') }}">Chi sono</a></li>
  <li><a href="{{ url('/#skills') }}">Skills</a></li>
  <li><a href="{{ url('/#projects') }}">Miei progetti</a></li>
  <li><a href="{{ url('/#contact') }}">Contattami</a></li>
  <li><a href="{{ route('privacy') }}">Privacy policy</a></li>
</ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

    <section id="hero" class="hero section dark-background">
  <img src="assets/img/hero-bg-2.jpg" alt="" class="hero-bg">

  <div class="container">
    <div class="row gy-4 justify-content-between">
      <div class="col-lg-4 order-lg-last hero-img" data-aos="zoom-out" data-aos-delay="100">
        @if($hero && $hero->hero_image)
          <img src="{{ asset($hero->hero_image) }}" class="img-fluid animated" alt="Hero Image">
        @endif
      </div>

      <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-in">
        <h1>
          {{ $hero->hero_title ?? 'Build Your Landing Page With' }}
          <span>{{ $hero->hero_name ?? 'Bootslander' }}</span>
        </h1>
        <p>{{ $hero->hero_subtitle ?? 'We are team of talented designers making websites with Bootstrap' }}</p>

      </div>
    </div>
  </div>

  <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" viewBox="0 24 150 28" preserveAspectRatio="none">
    <defs>
      <path id="wave-path" d="M-160 44c30 0 58-18 88-18s58 18 88 18 58-18 88-18 58 18 88 18v44h-352z"></path>
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
    <section class="section py-5">
        <div class="container text-center">
            <h1 class="display-1 text-dark fw-bold">403 - Accesso negato</h1>
            <p class="text-muted">Torna alla <a href="{{ url('/') }}">home</a></p>
        </div>
    </section>

  <footer id="footer" class="footer dark-background">



    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Bootslander</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>
<!-- Vendor JS Files -->
<script src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('frontend/assets/vendor/php-email-form/validate.js') }}"></script>
<script src="{{ asset('frontend/assets/vendor/aos/aos.js') }}"></script>
<script src="{{ asset('frontend/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{ asset('frontend/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
<script src="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

<!-- Main JS File -->
<script src="{{ asset('frontend/assets/js/main.js') }}"></script>

{!! $renderAnalytics ?? '' !!}
</body>

</html>
