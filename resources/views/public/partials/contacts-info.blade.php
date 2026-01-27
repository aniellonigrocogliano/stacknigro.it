<div class="col-lg-4">
  @foreach($contacts as $contact)
    <div class="info-item d-flex" data-aos="fade-up">
      <i class="{{ $contact->fa_icon }} flex-shrink-0"></i>
      <div>
        <h3>{{ $contact->name }}</h3>

        @php
          $value = $contact->value;
          $type  = strtolower($contact->name);
        @endphp

        <p>
          @if(str_contains($type, 'email'))
            <a href="mailto:{{ $value }}">{{ $value }}</a>

          @elseif(str_contains($type, 'pec'))
            <a href="mailto:{{ $value }}">{{ $value }}</a>

          @elseif(str_contains($type, 'telefono'))
            <a href="tel:{{ preg_replace('/\s+/', '', $value) }}">{{ $value }}</a>

          @elseif(str_contains($type, 'whatsapp'))
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $value) }}" target="_blank">
              {{ $value }}
            </a>

          @elseif(str_contains($type, 'linkedin') || str_contains($type, 'github'))
            <a href="{{ $value }}" target="_blank" rel="noopener">
              {{ $value }}
            </a>

          @else
            {{ $value }}
          @endif
        </p>

      </div>
    </div>
  @endforeach
</div>
