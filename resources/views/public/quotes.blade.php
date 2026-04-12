@extends('layouts.public')

@section('title', 'Preventivo')

@section('content')

{{-- ===========================
      SEZIONE CARD PACCHETTI
      =========================== --}}
<section id="pricing" class="pricing section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Pricing</h2>
    <div><span>Scegli il tuo</span> <span class="description-title">Pacchetto</span></div>
  </div>

  <div class="container" id="container-packages">
    <div class="mb-5 text-center" data-aos="fade-up">
        <button type="button" class="px-5 py-3 btn btn-primary rounded-pill fw-bold btn-show-wizard">
            Nessun pacchetto ti soddisfa? Calcola il tuo prezzo personalizzato<i class="fa-solid fa-arrow-right ms-2"></i>
        </button>
    </div>

    <div class="row gy-4">
      @foreach($packages as $index => $p)
        <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="{{ ($index + 1) * 100 }}">
          <div class="text-center pricing-item d-flex flex-column align-items-center">
            <div class="mb-3 d-flex justify-content-center w-100">
              <i class="{{ $p->icon ?? 'fa-solid fa-box' }}" style="font-size: 45px; color: var(--accent-color);"></i>
            </div>
            <h3 class="text-center w-100">{{ $p->name }}</h3>
            <p class="description">{{ $p->description }}</p>

            @if($p->real_value > $p->promo_price)
              <h5 class="mt-3 mb-0 text-danger text-decoration-line-through" style="font-size: 1.1rem; opacity: 0.8;">
                € {{ number_format($p->real_value, 0, ',', '.') }}
              </h5>
            @endif

            <h4 class="{{ $p->real_value > $p->promo_price ? 'mt-1' : 'mt-4' }}">
              <sup>€</sup>{{ number_format($p->promo_price, 0, ',', '.') }}<span> totale</span>
            </h4>

            {{-- TEMPO DI CONSEGNA STIMATO --}}
            @php
                $totalHours = $p->options->sum('hours');
                $estimatedDays = $totalHours > 0 ? ceil($totalHours / 8) : 0;
            @endphp
            @if($estimatedDays > 0)
                <div class="mt-2 mb-4 text-center text-muted w-100" style="font-size: 0.95rem;">
                    <i class="bi bi-clock-history me-1"></i> Tempo di consegna stimato: <strong>{{ $estimatedDays }} giorni lavorativi</strong>
                </div>
            @else
                <div class="mt-2 mb-4"></div>
            @endif

            <button type="button" class="border-0 cta-btn btn-buy-package w-100"
                    data-package-id="{{ $p->id }}"
                    data-package-name="{{ $p->name }}"
                    data-package-slug="{{ $p->slug }}">
              Acquista / Info
            </button>

            <ul class="mt-4 text-start w-100 list-unstyled">
                @php $currentLevelName = null; @endphp
                @foreach($p->options as $opt)
                    @php
                        $firstLevel = $opt->levels->first();
                        $levelName = $firstLevel ? $firstLevel->name : 'Altro';
                    @endphp

                    @if($currentLevelName !== $levelName)
                        <li class="mt-3 mb-1 fw-bold text-dark" style="font-size: 0.95rem; border-bottom: 1px solid #eee;">
                            {{ $levelName }}
                        </li>
                        @php $currentLevelName = $levelName; @endphp
                    @endif
                    <li class="mb-1 ps-2 d-flex align-items-start" style="font-size: 0.9rem;">
                        <i class="bi bi-check2 text-success me-2 fw-bold"></i>
                        <span>{{ $opt->name }}</span>
                    </li>
                @endforeach
            </ul>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-5 text-center" data-aos="fade-up">
        <button type="button" class="px-5 py-3 btn btn-primary rounded-pill fw-bold btn-show-wizard">
            Non hai trovato quello che cerchi? Crea il tuo preventivo su misura <i class="fa-solid fa-magic ms-2"></i>
        </button>
    </div>
  </div>
</section>

{{-- ===========================
      SEZIONE CONTATTO PACCHETTO
      =========================== --}}
<section id="package-contact-section" class="section" style="display: none;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="p-4 text-center shadow-sm col-lg-8 card position-relative">
        <button type="button" class="btn btn-sm btn-outline-secondary btn-back-to-packages position-absolute" style="top: 15px; right: 15px;">
            <i class="bi bi-arrow-left me-1"></i> Indietro
        </button>
        <div class="mb-4 text-center">
          <h3 class="fw-bold">Riepilogo Pacchetto</h3>
          <p class="text-muted small">Stai richiedendo informazioni per:</p>
        </div>
        <div id="package-summary-display" class="p-4 mb-5 border rounded shadow-sm pricing-item d-flex flex-column align-items-center"
             style="background-color: #F6FDFB; border-color: #e0f2ee !important; border-radius: 15px;">
        </div>
        <div id="packageFormWrap" class="text-start">
            @include('public.partials.contact-form', [
                'action' => route('public.quotes.store'),
                'source' => 'pacchetti',
                'mode'   => 'send',
                'hideSubmit' => true
            ])
        </div>
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-outline-secondary btn-back-to-packages">Cambia pacchetto</button>
            <button type="button" id="btnSubmitPackage" class="px-5 py-2 btn btn-primary fw-bold">Invia Richiesta</button>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ===========================
      SEZIONE WIZARD PREVENTIVO
      =========================== --}}
<section class="section" id="preventivo" style="display: none;">
  <div class="container section-title" data-aos="fade-up">
    <h2>Preventivo</h2>
    <div><span>Calcola il tuo</span> <span class="description-title">preventivo</span></div>
  </div>

  <div class="container" data-aos="fade-up">
    @php
      $activeLevels = $levels;
      $levelCount   = $activeLevels->count();
      $totalSteps   = $levelCount + 4;
      $stepChoice   = $levelCount + 1;
      $stepCaptcha  = $levelCount + 2;
      $stepSend     = $levelCount + 3;
      $stepResult   = $levelCount + 4;
    @endphp

    <div class="mb-4" id="wizardStepper">
      <div class="flex-wrap gap-2 d-flex justify-content-center">
        @for($i=1; $i<=$totalSteps; $i++)
          <button type="button" class="btn btn-sm btn-outline-secondary step-badge" data-step="{{ $i }}" disabled>Step {{ $i }}</button>
        @endfor
      </div>
    </div>

    <div id="quoteWizard">
      @foreach($activeLevels as $index => $level)
        @php
            $isTargetOfShowLevel = $rules->where('action_type', 'show_level')->pluck('target_level_id')->contains($level->id);
        @endphp
        <div class="mb-3 card quote-step border-0 shadow-sm level-container {{ $isTargetOfShowLevel ? 'rule-hidden-step' : '' }}"
             data-step="{{ $index + 1 }}"
             data-level-id="{{ $level->id }}"
             data-selection-type="{{ $level->selection_type }}"
             style="display:none;">
          <div class="p-4 card-body">
            <h4 class="mb-1 fw-bold">{{ $level->name }}</h4>
            <p class="mb-3 text-muted small">
                {{ $level->selection_type === 'single' ? 'Selezione obbligatoria' : 'Selezione multipla' }}
            </p>
            <div class="gap-2 vstack">
              @foreach($level->options as $opt)
                @php
                    $isTargetOfShowOpt = in_array($opt->id, $ruleTargetOptionIds->toArray());
                @endphp
                <label class="border rounded p-3 option-item {{ $isTargetOfShowOpt ? 'd-none' : '' }}"
                        data-option-id="{{ $opt->id }}" data-price="{{ $opt->price ?? 0 }}" data-hours="{{ $opt->hours ?? 0 }}">
                  <div class="gap-2 d-flex align-items-start">
                    <input class="mt-1 form-check-input quote-option"
                           type="{{ $level->selection_type === 'single' ? 'radio' : 'checkbox' }}"
                           name="level_{{$level->id}}{{ $level->selection_type === 'single' ? '' : '[]' }}"
                           value="{{ $opt->id }}">
                    <div class="w-100">
                        <div class="fw-semibold">{{ $opt->name }}</div>
                        @if($opt->description)<div class="small text-muted">{{ $opt->description }}</div>@endif
                    </div>
                  </div>
                </label>
              @endforeach
            </div>
            <div class="mt-4 d-flex justify-content-between">
              <button type="button" class="btn btn-outline-secondary btn-prev {{ ($index + 1) === 1 ? 'btn-back-to-packages' : '' }}">Indietro</button>
              <button type="button" class="px-4 btn btn-primary btn-next">Avanti</button>
            </div>
          </div>
        </div>
      @endforeach

      <div class="mb-3 border-0 shadow-sm card quote-step" data-step="{{ $stepChoice }}" style="display:none;">
        <div class="p-4 text-center card-body">
          <h4 class="fw-bold">Come vuoi procedere?</h4>
          <div class="gap-2 mt-4 d-flex justify-content-center">
            <button type="button" class="btn btn-outline-secondary btn-prev">Indietro</button>
            <button type="button" class="btn btn-outline-dark" id="btnGoAnonymous">Calcola anonimo</button>
            <button type="button" class="btn btn-primary" id="btnGoSend">Voglio inviartelo</button>
          </div>
          <form id="anonForm" method="POST" action="{{ route('public.quotes.store') }}">
            @csrf
            <input type="hidden" name="mode" value="anonymous">
            <input type="hidden" name="source" value="preventivo">
            <input type="hidden" name="quote_payload" id="anon_quote_payload">
            <input type="hidden" name="quote_summary" id="anon_quote_summary">
            <input type="hidden" name="snct" id="anon_snct"><input type="hidden" name="sncs" id="anon_sncs">
          </form>
        </div>
      </div>

      <div class="mb-3 border-0 shadow-sm card quote-step" data-step="{{ $stepCaptcha }}" style="display:none;" id="captchaStepCard">
        <div class="p-4 text-center card-body">
          <h4 class="fw-bold">Verifica di sicurezza</h4>
          <div class="sn-captcha" data-sitekey="sn_y1bsbkwrlfntoawzkefm86op3ddd8rm5" data-theme="standard"></div>

          <div class="mt-4 mb-3 text-start d-flex justify-content-center">
              <div class="form-check" style="max-width: 400px;">
                  <input class="form-check-input" type="checkbox" id="privacyAnon" name="privacy_accepted" value="1">
                  <label class="form-check-label small text-muted" for="privacyAnon">
                      Ho letto e accetto la <a href="/privacy-policy" target="_blank">Privacy Policy</a> e il trattamento dei dati.
                  </label>
              </div>
          </div>

          <div class="mt-4 d-flex justify-content-center">
            <button type="button" class="btn btn-outline-secondary me-2" id="btnCaptchaBack">Indietro</button>
            <button type="button" class="btn btn-primary" id="btnCaptchaContinue">Continua</button>
          </div>
        </div>
      </div>

      <div class="mb-3 border-0 shadow-sm card quote-step" data-step="{{ $stepSend }}" style="display:none;">
        <div class="p-4 card-body">
          <h4 class="fw-bold">Inserisci i tuoi dati</h4>
          <div id="contactFormWizardWrap">
            @include('public.partials.contact-form', [
                'action' => route('public.quotes.store'),
                'source' => 'preventivo',
                'hideSubmit' => true,
                'mode' => 'send'
            ])
          </div>
          <div class="mt-4 d-flex">
            <button type="button" class="btn btn-outline-secondary me-2" id="btnSendBack">Indietro</button>
            <button type="button" class="btn btn-primary" id="btnSendNow">Invia con i miei dati</button>
          </div>
        </div>
      </div>

      <div class="mb-3 border-0 shadow-sm card quote-step" data-step="{{ $stepResult }}" style="display:none;" id="resultStepCard">
        <div class="p-4 text-center card-body">
          <h4 class="fw-bold">Il tuo preventivo</h4>
          <div class="p-4 mb-3 border rounded shadow-sm" style="background-color: #F6FDFB;">
             <div id="resultPrice" class="mb-1 fs-2 fw-bold text-success"></div>
             <div id="resultWeeksDays" class="text-muted fs-5"></div>
          </div>
          <ul id="resultList" class="text-start small list-group list-group-flush"></ul>
          <button type="button" class="px-5 mt-4 btn btn-primary btn-back-to-packages rounded-pill">Torna ai pacchetti</button>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- MODALE DI SUCCESSO --}}
<div class="modal fade" id="quoteAlertModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="border-0 shadow-lg modal-content" style="border-radius: 15px;">
      <div class="p-5 text-center modal-body">
        <div class="mb-3"><i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i></div>
        <h4 id="quoteAlertTitle" class="fw-bold"></h4>
        <p id="quoteAlertBody" class="mb-4 text-muted"></p>
        <button type="button" class="px-5 btn btn-primary rounded-pill fw-bold" data-bs-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rules = @json($rulesForJs);
    const targetLevelIds = @json($ruleTargetLevelIds);
    const totalSteps = {{ $totalSteps }};
    const stepCaptcha = {{ $stepCaptcha }};
    const stepSend = {{ $stepSend }};
    const stepResult = {{ $stepResult }};
    let currentStep = 1;

    const sectionPricing = document.getElementById('pricing');
    const sectionWizard  = document.getElementById('preventivo');
    const sectionPkgForm = document.getElementById('package-contact-section');

    function trackGA4(eventName, params = {}) {
        if (typeof gtag === 'function') { gtag('event', eventName, params); }
    }

    function showModalAlert(message, title = 'Notifica') {
        const el = document.getElementById('quoteAlertModal');
        document.getElementById('quoteAlertTitle').textContent = title;
        document.getElementById('quoteAlertBody').textContent = message;
        new bootstrap.Modal(el).show();
    }

    function applyRules() {
        const selectedOptionIds = Array.from(document.querySelectorAll('.level-container:not(.rule-hidden-step) .option-item:not(.d-none) .quote-option:checked')).map(el => parseInt(el.value));
        const levelsToHideByDefault = rules.filter(r => r.action_type === 'show_level').map(r => r.target_level_id);
        const optionsToHideByDefault = rules.filter(r => ['show_option', 'require_option'].includes(r.action_type)).map(r => r.target_option_id);

        document.querySelectorAll('.level-container').forEach(lc => {
            const lid = parseInt(lc.dataset.levelId);
            if (levelsToHideByDefault.includes(lid)) lc.classList.add('rule-hidden-step');
            else lc.classList.remove('rule-hidden-step');
        });

        document.querySelectorAll('.option-item').forEach(oi => {
            const oid = parseInt(oi.dataset.optionId);
            if (optionsToHideByDefault.includes(oid)) {
                oi.classList.add('d-none');
                const inp = oi.querySelector('input'); if (inp) inp.checked = false;
            } else oi.classList.remove('d-none');
        });

        rules.forEach(rule => {
            const isTriggerActive = selectedOptionIds.includes(rule.trigger_option_id);
            if (isTriggerActive) {
                if (rule.action_type === 'show_level') document.querySelector(`.level-container[data-level-id="${rule.target_level_id}"]`)?.classList.remove('rule-hidden-step');
                else if (rule.action_type === 'hide_level') {
                    const targetLvl = document.querySelector(`.level-container[data-level-id="${rule.target_level_id}"]`);
                    if (targetLvl) { targetLvl.classList.add('rule-hidden-step'); targetLvl.querySelectorAll('input:checked').forEach(inp => inp.checked = false); }
                } else if (rule.action_type === 'show_option') document.querySelector(`.option-item[data-option-id="${rule.target_option_id}"]`)?.classList.remove('d-none');
                else if (rule.action_type === 'require_option') {
                    const targetOpt = document.querySelector(`.option-item[data-option-id="${rule.target_option_id}"]`);
                    if (targetOpt) { targetOpt.classList.remove('d-none'); const inp = targetOpt.querySelector('input'); if (inp && !inp.checked) inp.checked = true; }
                } else if (rule.action_type === 'hide_option') {
                    const targetOpt = document.querySelector(`.option-item[data-option-id="${rule.target_option_id}"]`);
                    if (targetOpt) { targetOpt.classList.add('d-none'); const inp = targetOpt.querySelector('input'); if (inp) inp.checked = false; }
                }
            }
        });
    }

    function calc() {
        let p = 0, h = 0; const list = [];
        document.querySelectorAll('.quote-step:not(.rule-hidden-step) .option-item:not(.d-none) .quote-option:checked').forEach(i => {
            const w = i.closest('.option-item');
            p += parseFloat(w.dataset.price); h += parseFloat(w.dataset.hours);
            list.push(w.querySelector('.fw-semibold').textContent);
        });
        document.getElementById('resultPrice').textContent = '€ ' + p.toLocaleString('it-IT');
        document.getElementById('resultWeeksDays').textContent = Math.ceil(h/8) + ' giorni lavorativi stimati';
        document.getElementById('resultList').innerHTML = list.map(li => `<li class="px-0 bg-transparent border-0 list-group-item"><i class="bi bi-check2 text-success me-2"></i>${li}</li>`).join('');
        return { p, h, list };
    }

    function showStep(n) {
        const stepDir = n > currentStep ? 1 : -1;
        let targetStep = n;
        while (targetStep >= 1 && targetStep <= totalSteps) {
            const targetEl = document.querySelector(`.quote-step[data-step="${targetStep}"]`);
            if (targetEl && targetEl.classList.contains('rule-hidden-step')) targetStep += stepDir;
            else break;
        }
        if (targetStep > totalSteps || targetStep < 1) return;
        currentStep = targetStep;
        document.querySelectorAll('.quote-step').forEach(el => el.style.display = (parseInt(el.dataset.step) === currentStep) ? '' : 'none');
        document.querySelectorAll('.step-badge').forEach(b => {
            const active = parseInt(b.dataset.step) === currentStep;
            b.classList.toggle('btn-primary', active); b.classList.toggle('btn-outline-secondary', !active);
        });
        trackGA4('wizard_progress', { step_number: currentStep, step_label: currentStep === stepResult ? 'Fine' : 'Step ' + currentStep });
        window.scrollTo({ top: sectionWizard.offsetTop - 50, behavior: 'smooth' });
    }

    document.querySelectorAll('.quote-option').forEach(input => {
        input.addEventListener('change', () => { applyRules(); calc(); });
    });

    document.querySelectorAll('.btn-next').forEach(b => b.addEventListener('click', () => {
        const currentPanel = document.querySelector(`.quote-step[data-step="${currentStep}"]`);
        if(currentPanel && currentPanel.dataset.selectionType === 'single') {
            if(!currentPanel.querySelector('.option-item:not(.d-none) .quote-option:checked')) {
                showModalAlert("Per favore, effettua una scelta per proseguire.", "Selezione obbligatoria");
                return;
            }
        }
        showStep(currentStep + 1);
    }));

    document.querySelectorAll('.btn-prev').forEach(b => b.addEventListener('click', () => showStep(currentStep - 1)));

    async function submitQuoteAjax(formData) {
        try {
            const response = await fetch("{{ route('public.quotes.store') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            });

            if(!response.ok) { console.error("Errore validazione"); return; }

            trackGA4('generate_lead', { source: formData.get('source'), method: formData.get('mode') });

            // LOGICA MODAL DI SUCCESSO MANUALE PER AJAX
            const isAnon = formData.get('mode') === 'anonymous';
            const msg = isAnon
                ? "Il tuo preventivo è stato calcolato correttamente."
                : "Preventivo inviato con successo! Riceverai una risposta dettagliata all'email fornita.";

            showModalAlert(msg, "Ricevuto!");

            calc();
            showStep(stepResult);
        } catch (error) {
            console.error("Errore AJAX:", error);
            calc(); showStep(stepResult);
        }
    }

    document.getElementById('btnSubmitPackage').addEventListener('click', () => {
        const form = document.querySelector('#packageFormWrap form');
        if(form) {
            let sourceInput = form.querySelector('input[name="source"]');
            if(!sourceInput) { sourceInput = document.createElement('input'); sourceInput.type = 'hidden'; sourceInput.name = 'source'; form.appendChild(sourceInput); }
            sourceInput.value = 'pacchetti'; form.submit();
        }
    });

    document.getElementById('btnSendNow').addEventListener('click', () => {
        const { p, h, list } = calc();
        const form = document.querySelector('#contactFormWizardWrap form');
        if(form) {
            const formData = new FormData(form);
            formData.append('quote_payload', JSON.stringify(list));
            formData.append('quote_summary', "Prezzo stimato: €" + p + "\nScelte:\n- " + list.join('\n- '));
            formData.set('mode', 'send'); formData.set('source', 'preventivo');
            submitQuoteAjax(formData);
        }
    });

    document.getElementById('btnCaptchaContinue').addEventListener('click', () => {
        const privacyCheck = document.getElementById('privacyAnon');
        if (!privacyCheck.checked) { showModalAlert("Devi accettare la privacy policy per continuare.", "Attenzione"); return; }
        const { p, h, list } = calc();
        const form = document.getElementById('anonForm');
        const formData = new FormData(form);
        const captchaStep = document.getElementById('captchaStepCard');
        formData.set('privacy_accepted', '1');
        formData.append('snct', captchaStep.querySelector('input[name="snct"]')?.value);
        formData.append('sncs', captchaStep.querySelector('input[name="sncs"]')?.value);
        formData.set('quote_payload', JSON.stringify(list));
        formData.set('quote_summary', "Prezzo stimato: €" + p + "\nScelte: " + list.join(', '));
        formData.set('source', 'preventivo');
        document.querySelectorAll('.quote-option:checked').forEach(inp => formData.append(inp.name, inp.value));
        submitQuoteAjax(formData);
    });

    document.querySelectorAll('.btn-show-wizard').forEach(btn => btn.addEventListener('click', () => {
        sectionPricing.style.display = 'none'; sectionWizard.style.display = 'block';
        trackGA4('wizard_start', { type: 'custom_quote' }); showStep(1);
    }));

    document.querySelectorAll('.btn-buy-package').forEach(b => b.addEventListener('click', function() {
        const pId = this.dataset.packageId; const pName = this.dataset.packageName; const pSlug = this.dataset.packageSlug;
        const parentCard = this.closest('.pricing-item');
        trackGA4('select_content', { content_type: 'package', item_id: pName || pId });
        if (pSlug) { const newUrl = window.location.origin + '/preventivo/' + pSlug; window.history.pushState({ path: newUrl }, '', newUrl); }
        const form = document.querySelector('#packageFormWrap form');
        let inputId = form.querySelector('input[name="package_id"]');
        if(!inputId) { inputId = document.createElement('input'); inputId.type='hidden'; inputId.name='package_id'; form.appendChild(inputId); }
        inputId.value = pId;
        const summary = document.getElementById('package-summary-display'); summary.innerHTML = '';
        summary.appendChild(parentCard.querySelector('.mb-3').cloneNode(true));
        summary.appendChild(parentCard.querySelector('h3').cloneNode(true));
        if(parentCard.querySelector('h5')) summary.appendChild(parentCard.querySelector('h5').cloneNode(true));
        summary.appendChild(parentCard.querySelector('h4').cloneNode(true));
        const timeEstimate = parentCard.querySelector('.text-muted.w-100.text-center');
        if (timeEstimate && timeEstimate.innerHTML.trim() !== '') { summary.appendChild(timeEstimate.cloneNode(true)); }
        summary.appendChild(parentCard.querySelector('ul').cloneNode(true));
        sectionPricing.style.display = 'none'; sectionPkgForm.style.display = 'block';
        window.scrollTo({ top: sectionPkgForm.offsetTop - 50, behavior: 'smooth' });
    }));

    document.querySelectorAll('.btn-back-to-packages').forEach(b => b.addEventListener('click', () => {
        const baseUrl = window.location.origin + '/preventivo'; window.history.pushState({ path: baseUrl }, '', baseUrl);
        sectionWizard.style.display = 'none'; sectionPkgForm.style.display = 'none'; sectionPricing.style.display = 'block';
        window.scrollTo({ top: sectionPricing.offsetTop - 50, behavior: 'smooth' });
    }));

    document.getElementById('btnGoAnonymous').addEventListener('click', () => showStep(stepCaptcha));
    document.getElementById('btnGoSend').addEventListener('click', () => { calc(); showStep(stepSend); });

    @if(isset($selectedPackage))
        const selId = "{{ $selectedPackage->id }}";
        const target = document.querySelector(`.btn-buy-package[data-package-id="${selId}"]`);
        if(target) setTimeout(() => { target.click(); }, 300);
    @endif

    @if(session('success'))
        showModalAlert("{{ session('success') }}", "Ricevuto!");
    @endif

    applyRules(); calc();
});
</script>

<style>
.rule-hidden-step { display: none !important; }
</style>
@endpush
