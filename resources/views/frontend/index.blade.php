<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Stacknigro</title>
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
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">Chi sono</a></li>
          <li><a href="#skills">Skills</a></li>
          <li><a href="#projects">Miei proggetti</a></li>
          <li><a href="#contact">Contattami</a></li>
          <li><a href="#pricing">Privacy policy</a></li>
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

<!-- About Section -->
<section id="about" class="about section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Chi sono</h2>
        <div><span>Scopri di</span> <span class="description-title">Più</span></div>
      </div><!-- End Section Title -->

    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card p-4 shadow">
          <div class="content text-start">
            {!! $bio?->content ?? '<p>Contenuto non disponibile.</p>' !!}
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
<!-- /About Section -->

    <!-- Skills Section -->
<section id="skills" class="features section">
  <div class="container">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Skills</h2>
        <div><span>Le mie</span> <span class="description-title">competenze</span></div>
      </div><!-- End Section Title -->

    <div class="row gy-4">
      @foreach($skills as $index => $skill)
        <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 100) }}">
          <div class="features-item">
            <i class="{{ $skill->icon }}" style="color: {{ $skill->color }}; font-size: 2.5rem;"></i>
            <h3><a href="#" class="stretched-link">{{ $skill->name }}</a></h3>
          </div>
        </div>
      @endforeach
    </div>

  </div>
</section>
<!-- /Skills Section -->

   <!-- Projects Section -->
<section id="projects" class="team section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Progetti</h2>
    <div><span>Dai un'occhiata ai</span> <span class="description-title">Progetti</span></div>
  </div>

  <div class="container">
    <div class="row gy-5">
      @foreach($projects as $project)
        <div class="col-lg-4 col-md-6" data-aos="fade-up">
          <div class="member">
            <div class="pic">
              <div id="carouselProject{{ $project->id }}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                  @foreach (range(1,5) as $i)
                    @php $imgField = "image_$i"; @endphp
                    @if(!empty($project->$imgField))
                      <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <img src="{{ asset($project->$imgField) }}" class="d-block w-100" alt="Immagine progetto {{ $i }}">
                      </div>
                    @endif
                  @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselProject{{ $project->id }}" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselProject{{ $project->id }}" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
              </div>
            </div>
            <div class="member-info mt-3">
              <h4>{{ $project->title }}</h4>
              <span>{{ $project->description }}</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

</section>


    <!-- Contact Section -->
<section id="contact" class="contact section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Contatti</h2>
    <div><span>Contattami</span> <span class="description-title">Ora</span></div>
  </div>

  <div class="container" data-aos="fade" data-aos-delay="100">
    <div class="row gy-4">

      <div class="col-lg-4">
        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
          <i class="bi bi-envelope flex-shrink-0"></i>
          <div>
            <h3>Email</h3>
            <p>{{ $contact->email }}</p>
          </div>
        </div>

        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
          <i class="bi bi-mailbox flex-shrink-0"></i>
          <div>
            <h3>PEC</h3>
            <p>{{ $contact->pec }}</p>
          </div>
        </div>

        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
          <i class="bi bi-telephone flex-shrink-0"></i>
          <div>
            <h3>Telefono</h3>
            <p>{{ $contact->phone }}</p>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <form action="{{ route('contact.send') }}" method="POST" class="php-email-form">
  @csrf
  <div class="row gy-4">
    <div class="col-md-6">
      <input type="text" name="first_name" class="form-control" placeholder="Nome" required>
    </div>
    <div class="col-md-6">
      <input type="text" name="last_name" class="form-control" placeholder="Cognome" required>
    </div>
    <div class="col-md-6">
      <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="col-md-6">
      <input type="text" name="phone" class="form-control" placeholder="Telefono" required>
    </div>
    <div class="col-md-12">
      <textarea name="message" class="form-control" rows="6" placeholder="Messaggio" required></textarea>
    </div>
    <div class="col-md-12 text-center">
      <button type="submit">Invia messaggio</button>
    </div>
  </div>
</form>
      </div>

    </div>
  </div>
</section>

  </main>

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
@include('partials.cookie-consent')
</html>
