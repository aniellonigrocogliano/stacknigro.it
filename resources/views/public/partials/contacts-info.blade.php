<div class="col-lg-4">
  @foreach($contacts as $contact)
    <div class="info-item d-flex" data-aos="fade-up">
      <i class="{{ $contact->fa_icon }} flex-shrink-0"></i>
      <div>
        <h3>{{ $contact->name }}</h3>

        @php
          $label = $contact->value;               // testo visibile
          $href  = $contact->href;                // link reale
          $blank = (bool) $contact->target_blank; // nuova scheda
        @endphp

        <p>
          @if(!empty($href))
            <a href="{{ $href }}"
               class="text-nowrap"
               @if($blank) target="_blank" rel="noopener" @endif>
              {{ $label }}
            </a>
          @else
            <span class="text-nowrap">{{ $label }}</span>
          @endif
        </p>

      </div>
    </div>
  @endforeach
</div>

