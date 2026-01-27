@extends('layouts.public')

@section('title', 'Preventivo')

@section('content')
<section class="section" id="preventivo">
  <div class="container section-title" data-aos="fade-up">
    <h2>Preventivo</h2>
    <div><span>Calcola il tuo</span> <span class="description-title">preventivo</span></div>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    @php
      $activeLevels = $levels;
      $levelCount   = $activeLevels->count();
      $totalSteps   = $levelCount + 2;
      $stepChoice   = $levelCount + 1; // scelta: anonimo vs invio
      $stepResult   = $levelCount + 2; // risultato pulito

      $showableOptionIds = collect($ruleTargetOptionIds ?? []);
    @endphp

    @if(session('success'))
      <div class="mb-3 alert alert-success" id="flashSuccess">{{ session('success') }}</div>
    @endif

    {{-- Stepper (verrà nascosto quando mostriamo solo il risultato) --}}
    <div class="mb-4" id="wizardStepper">
      <div class="flex-wrap gap-2 d-flex">
        @for($i=1; $i<=$totalSteps; $i++)
          <span class="badge rounded-pill bg-secondary step-badge" data-step="{{ $i }}">Step {{ $i }}</span>
        @endfor
      </div>
    </div>

    <div id="quoteWizard">

      {{-- ===========================
           STEPS LIVELLI (1..N)
           =========================== --}}
      @foreach($activeLevels as $index => $level)
        @php
          $stepNumber = $index + 1;

          $options = $level->options->filter(function($opt) use ($showableOptionIds) {
            return (int)$opt->is_default === 1 || $showableOptionIds->contains($opt->id);
          });

          $isSingle  = ($level->selection_type === 'single');
          $inputType = $isSingle ? 'radio' : 'checkbox';
          $nameAttr  = $isSingle ? "level_{$level->id}" : "level_{$level->id}[]";
        @endphp

        <div class="mb-3 card quote-step"
             data-step="{{ $stepNumber }}"
             data-level-id="{{ $level->id }}"
             style="display:none;">
          <div class="card-body">

            <h4 class="mb-1">{{ $level->name }}</h4>
            <div class="mb-3 text-muted">
              {{ $isSingle ? 'Selezione singola (obbligatoria)' : 'Selezione multipla (facoltativa)' }}
            </div>

            <div class="gap-2 vstack">
              @foreach($options as $opt)
                @php
                  $pivot = $opt->pivot;
                  $startHidden = false;
                  if ((int)$opt->is_default !== 1) $startHidden = true;
                  if ((int)($pivot->is_hidden_by_default ?? 0) === 1) $startHidden = true;
                @endphp

                <label class="border rounded p-3 option-item {{ $startHidden ? 'd-none' : '' }}"
                       data-option-id="{{ $opt->id }}"
                       data-level-id="{{ $level->id }}"
                       data-price="{{ $opt->price ?? 0 }}"
                       data-hours="{{ $opt->hours ?? 0 }}">
                  <div class="gap-2 d-flex align-items-start">
                    <input class="mt-1 form-check-input quote-option"
                           type="{{ $inputType }}"
                           name="{{ $nameAttr }}"
                           value="{{ $opt->id }}"
                           data-level-id="{{ $level->id }}"
                           data-option-id="{{ $opt->id }}">

                    <div>
                      <div class="fw-semibold">{{ $opt->name }}</div>
                      @if(!empty($opt->description))
                        <div class="text-muted small">{{ $opt->description }}</div>
                      @endif
                    </div>
                  </div>
                </label>
              @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-between">
              <button type="button" class="btn btn-outline-secondary btn-prev">Indietro</button>
              <button type="button" class="btn btn-primary btn-next">Avanti</button>
            </div>

          </div>
        </div>
      @endforeach

      {{-- ===========================
           STEP SCELTA (N+1)
           =========================== --}}
      <div class="mb-3 card quote-step" data-step="{{ $stepChoice }}" style="display:none;" id="choiceStepCard">
        <div class="card-body">

          <h4 class="mb-2">Come vuoi procedere?</h4>
          <p class="mb-4 text-muted" style="max-width: 820px;">
            Puoi calcolare il preventivo in modo anonimo oppure inviarmi i tuoi dati per ricevere un preventivo
            più specifico e personalizzato.
          </p>

          <div class="flex-wrap gap-2 mb-4 d-flex">
            <button type="button" class="btn btn-outline-secondary btn-prev">Indietro</button>
            <button type="button" class="btn btn-outline-dark" id="btnCalcAnonymous">
              Calcola anonimo
            </button>
            <button type="button" class="btn btn-primary" id="btnShowContactForm">
              Voglio inviartelo
            </button>
          </div>

          {{-- FORM CONTATTO: nascosta finché non clicchi "Voglio inviartelo" --}}
          <div id="contactFormBlock" class="mt-3" style="display:none;">
            <div class="p-3 border rounded">
              <h5 class="mb-2">Inserisci i tuoi dati</h5>
              <div class="mb-3 text-muted">
                Compila il form e accetta la privacy policy per inviare il preventivo.
              </div>

              <div id="contactFormWrap">
                @include('public.partials.contact-form', [
                  'action' => route('public.quotes.store'),
                  'source' => 'quote',
                  'hideSubmit' => true,   // usiamo il bottone sotto
                  'hidePrivacy' => false, // qui la privacy è obbligatoria (checkbox nel partial)
                  'mode' => 'send',
                  'quotePayload' => '',
                  'quoteSummary' => '',
                ])
              </div>

              <div class="flex-wrap gap-2 mt-3 d-flex">
                <button type="button" class="btn btn-outline-secondary" id="btnHideContactForm">Annulla</button>
                <button type="button" class="btn btn-primary" id="btnSendWithData">
                  Invia con i miei dati
                </button>
              </div>
            </div>
          </div>

          {{-- FORM ANONIMO (hidden) --}}
          <form id="anonForm" method="POST" action="{{ route('public.quotes.store') }}">
            @csrf
            <input type="hidden" name="mode" value="anonymous">
            <input type="hidden" name="source" value="quote">

            {{-- dati fake/anonimi (tu hai detto: array di dati) --}}
            <input type="hidden" name="name" value="Preventivo anonimo">
            <input type="hidden" name="email" value="anonimo@stacknigro.it">
            <input type="hidden" name="phone" value="">
            <input type="hidden" name="subject" value="Preventivo anonimo">
            <input type="hidden" name="how_found" value="">
            <input type="hidden" name="user_message" id="anon_user_message" value="">

            {{-- privacy forzata per anonimo --}}
            <input type="hidden" name="privacy_accepted" value="1">

            {{-- payload --}}
            <input type="hidden" name="quote_payload" id="anon_quote_payload">
            <input type="hidden" name="quote_summary" id="anon_quote_summary">
          </form>

        </div>
      </div>

      {{-- ===========================
           STEP RISULTATO (N+2) - PULITO
           =========================== --}}
      <div class="mb-3 card quote-step" data-step="{{ $stepResult }}" style="display:none;" id="resultStepCard">
        <div class="card-body">
          <h4 class="mb-3">Risultato</h4>

          <div class="p-3 mb-3 border rounded">
            <div class="d-flex justify-content-between">
              <div class="text-muted">Totale ore</div>
              <div class="fw-bold" id="resultHours">0</div>
            </div>
            <div class="mt-2 d-flex justify-content-between">
              <div class="text-muted">Totale prezzo</div>
              <div class="fw-bold" id="resultPrice">€ 0,00</div>
            </div>
          </div>

          <div class="mb-3">
            <div class="mb-2 fw-semibold">Riepilogo scelte</div>
            <ul class="mb-0" id="resultList"></ul>
          </div>

          {{-- NIENTE BOTTONI QUI --}}
        </div>
      </div>

    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

  const levels = @json($levelsForJs);
  const rules  = @json($rulesForJs);

  const totalSteps = {{ $totalSteps }};
  const stepChoice = {{ $stepChoice }};
  const stepResult = {{ $stepResult }};

  let currentStep = 1;

  const stepEls  = document.querySelectorAll('.quote-step');
  const badgeEls = document.querySelectorAll('.step-badge');

  function showStep(n) {
    currentStep = n;

    stepEls.forEach(el => {
      el.style.display = (parseInt(el.dataset.step, 10) === n) ? '' : 'none';
    });

    badgeEls.forEach(b => {
      const active = parseInt(b.dataset.step, 10) === n;
      b.classList.toggle('bg-primary', active);
      b.classList.toggle('bg-secondary', !active);
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function selectedOptionIds() {
    return Array.from(document.querySelectorAll('.quote-option:checked'))
      .map(i => parseInt(i.value, 10));
  }

  function optionElement(optionId) {
    return document.querySelector(`.option-item[data-option-id="${optionId}"]`);
  }

  function optionTitle(optionId) {
    const el = optionElement(optionId);
    if (!el) return `Opzione #${optionId}`;
    const t = el.querySelector('.fw-semibold');
    return t ? t.textContent.trim() : `Opzione #${optionId}`;
  }

  function calcTotals() {
    const ids = selectedOptionIds();
    let price = 0;
    let hours = 0;

    ids.forEach(id => {
      const el = optionElement(id);
      if (!el) return;
      price += parseFloat(el.dataset.price || '0');
      hours += parseFloat(el.dataset.hours || '0');
    });

    return { ids, price, hours };
  }

  function buildPayload() {
    const { ids, price, hours } = calcTotals();
    return {
      levels_count: levels.length,
      selected_option_ids: ids,
      selected_options: ids.map(id => ({ id, name: optionTitle(id) })),
      totals: { price, hours },
      created_at: new Date().toISOString()
    };
  }

  function buildSummary(payload) {
    const eur = payload.totals.price.toFixed(2).replace('.', ',');
    const list = payload.selected_options.map(o => `- ${o.name}`).join('\n');
    return `Preventivo (frontend)\n\nScelte:\n${list || '- (nessuna opzione selezionata)'}\n\nTotale ore: ${payload.totals.hours}\nTotale prezzo: € ${eur}`;
  }

  function applyRules() {
    const selected = new Set(selectedOptionIds());

    rules.forEach(r => {
      const trigger = parseInt(r.trigger_option_id, 10);
      const active  = selected.has(trigger);

      if (r.target_option_id) {
        const wrap = document.querySelector(`.option-item[data-option-id="${r.target_option_id}"]`);
        if (wrap) {
          if (r.action_type === 'show_option') {
            if (active) wrap.classList.remove('d-none');
          }
          if (r.action_type === 'hide_option') {
            if (active) {
              wrap.classList.add('d-none');
              const input = wrap.querySelector('.quote-option');
              if (input) input.checked = false;
            }
          }
        }
      }

      if (r.target_level_id) {
        const lvlPanel = document.querySelector(`.quote-step[data-level-id="${r.target_level_id}"]`);
        if (lvlPanel) {
          if (r.action_type === 'hide_level' && active) lvlPanel.dataset.hidden = "1";
          if (r.action_type === 'show_level' && active) lvlPanel.dataset.hidden = "0";
        }
      }
    });
  }

  function validateStep(step) {
    if (step >= 1 && step <= levels.length) {
      const panel = document.querySelector(`.quote-step[data-step="${step}"]`);
      if (!panel) return true;

      const levelId = parseInt(panel.dataset.levelId || '0', 10);
      const level = levels.find(l => l.id === levelId);
      if (!level) return true;

      if (level.selection_type === 'single') {
        const checked = document.querySelectorAll(`.quote-option[data-level-id="${levelId}"]:checked`);
        return checked.length === 1;
      }
      return true;
    }
    return true;
  }

  function nextStep() {
    if (!validateStep(currentStep)) {
      alert('Devi selezionare un’opzione per proseguire.');
      return;
    }

    let n = currentStep + 1;

    while (true) {
      const panel = document.querySelector(`.quote-step[data-step="${n}"]`);
      if (!panel) break;
      if (panel.dataset.hidden === "1") { n++; continue; }
      break;
    }

    showStep(Math.min(n, totalSteps));
  }

  function prevStep() {
    let n = currentStep - 1;

    while (n > 0) {
      const panel = document.querySelector(`.quote-step[data-step="${n}"]`);
      if (!panel) break;
      if (panel.dataset.hidden === "1") { n--; continue; }
      break;
    }

    showStep(Math.max(n, 1));
  }

  document.addEventListener('change', (e) => {
    if (e.target.classList.contains('quote-option')) applyRules();
  });

  document.querySelectorAll('.btn-next').forEach(btn => btn.addEventListener('click', nextStep));
  document.querySelectorAll('.btn-prev').forEach(btn => btn.addEventListener('click', prevStep));

  // ===== RIEMPIMENTO HIDDEN + RISULTATO =====
  function setHiddenToForms(payload, summary) {
    // anon
    document.getElementById('anon_quote_payload').value = JSON.stringify(payload);
    document.getElementById('anon_quote_summary').value = summary;
    document.getElementById('anon_user_message').value = summary;

    // contact form (partial)
    const wrap = document.getElementById('contactFormWrap');
    if (wrap) {
      const payloadInput = wrap.querySelector('input[name="quote_payload"]');
      const summaryInput = wrap.querySelector('input[name="quote_summary"]');

      if (payloadInput) payloadInput.value = JSON.stringify(payload);
      if (summaryInput) summaryInput.value = summary;

    }

    // salva draft per reload (serve a mostrare risultato pulito dopo POST)
    localStorage.setItem('quoteDraft', JSON.stringify({ payload, summary }));
  }

  function renderResultFromPayload(payload, summary) {
    document.getElementById('resultHours').textContent = payload.totals.hours;
    document.getElementById('resultPrice').textContent = '€ ' + payload.totals.price.toFixed(2).replace('.', ',');

    const ul = document.getElementById('resultList');
    ul.innerHTML = '';

    if (!payload.selected_options || payload.selected_options.length === 0) {
      const li = document.createElement('li');
      li.textContent = '(nessuna opzione selezionata)';
      ul.appendChild(li);
    } else {
      payload.selected_options.forEach(o => {
        const li = document.createElement('li');
        li.textContent = o.name;
        ul.appendChild(li);
      });
    }
  }

  function buildAndStore() {
    const payload = buildPayload();
    const summary = buildSummary(payload);
    setHiddenToForms(payload, summary);
    return { payload, summary };
  }

  // RISULTATO PULITO: nascondo tutto tranne la card risultato
  function showCleanResultOnly(payload, summary) {
    // nascondo stepper
    const stepper = document.getElementById('wizardStepper');
    if (stepper) stepper.style.display = 'none';

    // nascondo tutti gli step
    document.querySelectorAll('.quote-step').forEach(el => el.style.display = 'none');

    // mostro solo risultato
    const resultCard = document.getElementById('resultStepCard');
    if (resultCard) resultCard.style.display = '';

    renderResultFromPayload(payload, summary);

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // ===== STEP SCELTA: UI =====
  document.getElementById('btnShowContactForm')?.addEventListener('click', () => {
    document.getElementById('contactFormBlock').style.display = '';
    // preparo payload/summary così quando invia è già tutto pronto
    buildAndStore();
  });

  document.getElementById('btnHideContactForm')?.addEventListener('click', () => {
    document.getElementById('contactFormBlock').style.display = 'none';
  });

  // ===== ANONIMO =====
  document.getElementById('btnCalcAnonymous')?.addEventListener('click', () => {
    // qui privacy viene forzata a 1 nel form anonimo, come richiesto
    const { payload, summary } = buildAndStore();
    // puoi anche mostrare subito risultato pulito PRIMA del submit (opzionale)
    // showCleanResultOnly(payload, summary);

    document.getElementById('anonForm').submit();
  });

  // ===== INVIO CON DATI =====
  document.getElementById('btnSendWithData')?.addEventListener('click', () => {
    // Qui DEVE essere accettata nel form vero
    const wrap = document.getElementById('contactFormWrap');
    const form = wrap ? wrap.querySelector('form') : null;
    if (!form) {
      alert('Form contatti non trovato.');
      return;
    }

    // verifica checkbox privacy (del partial)
    const chk = form.querySelector('input[name="privacy_accepted"]');
    if (!chk || !chk.checked) {
      alert('Per inviare il preventivo devi accettare la privacy policy.');
      return;
    }

    buildAndStore();
    form.submit(); // invia e basta
  });

  // ===== RIPRISTINO DOPO POST/REDIRECT BACK =====
  function restoreCleanResultAfterReload() {
    const hasSuccess = !!document.getElementById('flashSuccess');
    const draft = localStorage.getItem('quoteDraft');
    if (!hasSuccess || !draft) return false;

    try {
      const obj = JSON.parse(draft);
      if (!obj?.payload) return false;
      showCleanResultOnly(obj.payload, obj.summary || buildSummary(obj.payload));
      return true;
    } catch (e) {
      return false;
    }
  }

  // start
  applyRules();

  if (!restoreCleanResultAfterReload()) {
    showStep(1);
  }
});
</script>
@endpush
