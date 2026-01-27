<div class="col-lg-4">
  @foreach($contacts as $contact)
    <div class="info-item d-flex" data-aos="fade-up">
      <i class="{{ $contact->fa_icon }} flex-shrink-0"></i>
      <div>
        <h3>{{ $contact->name }}</h3>
        <p>{{ $contact->value }}</p>
      </div>
    </div>
  @endforeach
</div>
