<footer id="footer" class="footer dark-background">
  <div class="container py-4 text-center">

    {{-- Titolo --}}
    <p class="mb-3">La Famiglia StackNigro</p>

    {{-- 3 siti: logo + nome sulla stessa riga, tutti e tre sulla stessa riga --}}
    <div class="gap-4 mb-3 d-flex justify-content-center align-items-center flex-nowrap">

      <a href="https://stacknigro.it"
         class="gap-2 text-white d-inline-flex align-items-center text-decoration-none">
        <img src="https://stacknigro.it/storage/site/logo.webp" alt="Stacknigro.it" height="28">
        <span>Stacknigro.it</span>
      </a>

      <a href="https://captcha.stacknigro.it"
         class="gap-2 text-white d-inline-flex align-items-center text-decoration-none">
        <img src="https://captcha.stacknigro.it/assets/brand/logo.webp" alt="CAPTCHA Stacknigro" height="28">
        <span>CAPTCHA</span>
      </a>

      <a href="https://meteogesualdo.stacknigro.it/"
         class="gap-2 text-white d-inline-flex align-items-center text-decoration-none">
        <img src="https://stacknigro.it/storage/site/logo-meteo.webp" alt="Meteo" height="28">
        <span>Meteo</span>
      </a>

    </div>

    {{-- Copyright (senza logo) --}}
    <p class="mb-2">© {{ date('Y') }} <strong>Stacknigro.it</strong></p>

    {{-- Licenza template (intatta) --}}
    <p class="mb-1">© <strong class="px-1 sitename">Bootslander</strong> <span>All Rights Reserved</span></p>
    <div class="credits">
      Designed by <a href="https://bootstrapmade.com/" target="_blank">BootstrapMade</a>
    </div>

  </div>
</footer>
