<?php

namespace App\Http\Controllers;

use App\Models\CaptchaSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaptchaSiteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // ✅ Ora leggiamo last_used_at e last_ip direttamente dalla tabella captcha_sites
        $sites = CaptchaSite::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('domain', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.captcha_sites.index', compact('sites', 'q'));
    }

    public function create()
    {
        return view('admin.captcha_sites.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $captchaSite = new CaptchaSite();
        $captchaSite->name = $data['name'];
        $captchaSite->domain = $this->normalizeDomain($data['domain']);
        $captchaSite->domains_extra = $data['domains_extra'] ?? [];
        $captchaSite->is_active = (bool) ($data['is_active'] ?? true);
        $captchaSite->rate_limit_5m = (int) ($data['rate_limit_5m'] ?? 10);
        $captchaSite->rate_limit_day = (int) ($data['rate_limit_day'] ?? 500);
        $captchaSite->notes = $data['notes'] ?? null;

        // Generazione automatica chiavi
        $siteKey = CaptchaSite::generateSiteKey();
        while (CaptchaSite::where('site_key', $siteKey)->exists()) {
            $siteKey = CaptchaSite::generateSiteKey();
        }

        $captchaSite->site_key = $siteKey;
        $captchaSite->secret   = CaptchaSite::generateSecret();

        $captchaSite->save();

        // ✅ Non serve più creare righe in captcha_site_usage, usiamo la tabella principale

        return redirect()
            ->route('captcha-sites.edit', $captchaSite)
            ->with('success', 'Sito CAPTCHA creato. Site key generata.');
    }

    public function edit(CaptchaSite $captcha_site)
    {
        return view('admin.captcha_sites.edit', [
            'captchaSite' => $captcha_site,
        ]);
    }

    public function update(Request $request, CaptchaSite $captcha_site)
    {
        $data = $this->validated($request, (int) $captcha_site->id);

        $captcha_site->name = $data['name'];
        $captcha_site->domain = $this->normalizeDomain($data['domain']);
        $captcha_site->domains_extra = $data['domains_extra'] ?? [];
        $captcha_site->is_active = (bool) ($data['is_active'] ?? false);
        $captcha_site->rate_limit_5m = (int) ($data['rate_limit_5m'] ?? 10);
        $captcha_site->rate_limit_day = (int) ($data['rate_limit_day'] ?? 500);
        $captcha_site->notes = $data['notes'] ?? null;

        $captcha_site->save();

        return back()->with('success', 'Sito CAPTCHA aggiornato.');
    }

    public function destroy(CaptchaSite $captcha_site)
    {
        $captcha_site->delete();

        return redirect()
            ->route('captcha-sites.index')
            ->with('success', 'Sito CAPTCHA eliminato.');
    }

    /**
     * ✅ Statistiche del sito (ultimi 30 giorni da captcha_stats_daily)
     */
    public function stats(Request $request, CaptchaSite $captcha_site)
    {
        $days = (int) $request->query('days', 30);
        $days = max(1, min(365, $days));

        $from = now()->subDays($days)->toDateString();

        $stats = DB::table('captcha_stats_daily')
            ->where('site_id', $captcha_site->id)
            ->where('DAY', '>=', $from)
            ->orderBy('DAY', 'asc')
            ->get();

        $kpi = [
            'requests_total' => (int) $stats->sum('requests_total'),
            'requests_unique_ips' => (int) $stats->sum('requests_unique_ips'),
            'challenge_count' => (int) $stats->sum('challenge_count'),
            'verified_count' => (int) $stats->sum('verified_count'),
            'silent_count' => (int) $stats->sum('silent_count'),
            'pending_count' => (int) $stats->sum('pending_count'),
        ];

        $kpi['success_total'] = $kpi['silent_count'] + $kpi['verified_count'];

        $avgRiskValues = $stats->pluck('avg_risk_score')->filter(fn ($v) => $v !== null);
        $kpi['avg_risk_score'] = $avgRiskValues->isEmpty() ? null : round($avgRiskValues->avg(), 2);
        $kpi['max_risk_score'] = $stats->max('max_risk_score');

        return view('admin.captcha_sites.stats', [
            'captchaSite' => $captcha_site,
            'stats' => $stats,
            'kpi' => $kpi,
            'days' => $days,
        ]);
    }

    /**
     * Rigenera SOLO la secret (mai mostrata).
     */
    public function regenerateSecret(CaptchaSite $captcha_site)
    {
        $captcha_site->secret = CaptchaSite::generateSecret();
        $captcha_site->save();

        return back()->with('success', 'Secret rigenerata con successo.');
    }

    /**
     * Mostra la secret SOLO su richiesta (flash in session).
     */
    public function revealSecret(CaptchaSite $captcha_site)
    {
        return redirect()->to(url()->previous() . '#secret-anchor')
            ->with('sn_revealed_secret', $captcha_site->secret);
    }

    /**
     * Scarica ZIP di esempio
     */
    public function downloadKit(Request $request, CaptchaSite $captcha_site, string $type)
    {
        $type = strtolower(trim($type));
        abort_unless(in_array($type, ['php', 'laravel', 'node', 'serverless'], true), 404);

        $siteKey   = (string) $captcha_site->site_key;
        $secret    = (string) $captcha_site->secret;
        $verifyUrl = 'https://captcha.stacknigro.it/api/siteverify.php';

        $snippet = '<div class="sn-captcha" data-sitekey="' . $siteKey . '" data-theme="standard"></div>' . "\n"
                 . '<script src="https://captcha.stacknigro.it/widget.js?v=13" async></script>';

        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0775, true);
        }

        $safeName = preg_replace('/[^a-z0-9\-_.]+/i', '-', (string) ($captcha_site->name ?: $captcha_site->domain ?: 'site'));
        $zipName  = 'CaptchaStackNigro_' . ucfirst($type) . '_' . $safeName . '.zip';
        $zipPath  = $tmpDir . DIRECTORY_SEPARATOR . $zipName;

        if (file_exists($zipPath)) {
            @unlink($zipPath);
        }

        $zip = new \ZipArchive();
        abort_unless($zip->open($zipPath, \ZipArchive::CREATE) === true, 500, 'Impossibile creare lo ZIP');

        $readme =
            "STACKNIGRO CAPTCHA - ESEMPIO {$type}\n\n" .
            "FLUSSO CORRETTO\n" .
            "1) Inserisci il widget nel frontend usando la site key.\n" .
            "2) Il widget popola i campi snct (token) e sncs (session_id).\n" .
            "3) Il backend del tuo progetto deve inviare i dati a:\n" .
            "   {$verifyUrl}\n\n" .
            "PAYLOAD RICHIESTO\n" .
            "- secret\n" .
            "- token\n" .
            "- session_id\n" .
            "- ip\n\n" .
            "SNIPPET FRONTEND\n{$snippet}\n\n" .
            "IMPORTANTE\n" .
            "- La secret key deve restare solo sul backend.\n" .
            "- Non esporre mai la secret nel frontend.\n" .
            "- Questo ZIP contiene un esempio di integrazione server-side per {$type}.\n";

        $zip->addFromString('README.txt', $readme);
        $zip->addFromString('frontend-snippet.html', $snippet . "\n");

        if ($type === 'php') {
            $zip->addFromString('config.php', "<?php\n\nreturn [\n    'site_key' => '{$siteKey}',\n    'secret' => '{$secret}',\n    'verify_url' => '{$verifyUrl}',\n];\n");

            $zip->addFromString('example-form.php', <<<'PHP'
<?php
$config = require __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Esempio Stacknigro CAPTCHA</title>
</head>
<body>
    <form method="POST" action="verify-example.php">
        <input type="text" name="name" placeholder="Nome" required>
        <input type="email" name="email" placeholder="Email" required>

        <div class="sn-captcha" data-sitekey="<?= htmlspecialchars($config['site_key'], ENT_QUOTES) ?>" data-theme="standard"></div>
        <script src="https://captcha.stacknigro.it/widget.js?v=13" async></script>

        <button type="submit">Invia</button>
    </form>
</body>
</html>
PHP);

            $zip->addFromString('verify-example.php', <<<PHP
<?php

\$config = require __DIR__ . '/config.php';

if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metodo non consentito');
}

\$token = trim((string) (\$_POST['snct'] ?? ''));
\$sessionId = trim((string) (\$_POST['sncs'] ?? ''));

if (\$token === '' || \$sessionId === '') {
    exit('Verifica CAPTCHA mancante.');
}

\$payload = http_build_query([
    'secret'     => \$config['secret'],
    'token'      => \$token,
    'session_id' => \$sessionId,
    'ip'         => \$_SERVER['REMOTE_ADDR'] ?? '',
]);

\$opts = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-type: application/x-www-form-urlencoded\\r\\n",
        'content' => \$payload,
        'timeout' => 10,
    ],
];

\$context = stream_context_create(\$opts);
\$raw = @file_get_contents(\$config['verify_url'], false, \$context);

if (\$raw === false) {
    exit('Errore di verifica.');
}

\$json = json_decode(\$raw, true);

if (!is_array(\$json) || (\$json['success'] ?? false) !== true) {
    exit('CAPTCHA non valido.');
}

echo 'CAPTCHA valido. Procedi con la logica del form.';
PHP);
        }

        if ($type === 'laravel') {
            $zip->addFromString('.env.example', <<<ENV
SN_CAPTCHA_SITE_KEY={$siteKey}
SN_CAPTCHA_SECRET={$secret}
SN_CAPTCHA_VERIFY_URL={$verifyUrl}
ENV);

            $zip->addFromString('config-services-snippet.php', <<<'PHP'
'sn_captcha' => [
    'site_key'   => env('SN_CAPTCHA_SITE_KEY'),
    'secret'     => env('SN_CAPTCHA_SECRET'),
    'verify_url' => env('SN_CAPTCHA_VERIFY_URL', 'https://captcha.stacknigro.it/api/siteverify.php'),
],
PHP);

            $zip->addFromString('controller-example.php', <<<PHP
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExampleCaptchaController extends Controller
{
    public function store(Request \$request)
    {
        \$data = \$request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'snct' => ['required', 'string'],
            'sncs' => ['required', 'string'],
        ], [
            'snct.required' => 'Completa la verifica di sicurezza.',
            'sncs.required' => 'Sessione CAPTCHA mancante.',
        ]);

        \$secret = (string) config('services.sn_captcha.secret');
        \$verifyUrl = (string) config('services.sn_captcha.verify_url', '{$verifyUrl}');

        if (\$secret === '') {
            return back()->withInput()->withErrors([
                'snct' => 'Secret CAPTCHA mancante in configurazione.',
            ]);
        }

        try {
            \$payload = [
                'secret'     => \$secret,
                'token'      => \$data['snct'],
                'session_id' => \$data['sncs'],
                'ip'         => \$request->ip(),
            ];

            \$resp = Http::timeout(10)->asForm()->post(\$verifyUrl, \$payload);

            \$raw = (string) \$resp->body();
            \$json = json_decode(\$raw, true);
            \$jsonOk = is_array(\$json);

            if (!\$resp->ok() || !\$jsonOk) {
                Log::warning('SN CAPTCHA verify failed (HTTP fail or non-JSON)', [
                    'status' => \$resp->status(),
                    'session_id' => \$data['sncs'],
                ]);

                return back()->withInput()->withErrors([
                    'snct' => 'Verifica di sicurezza non superata. Riprova.',
                ]);
            }

            if ((\$json['success'] ?? false) !== true) {
                Log::warning('SN CAPTCHA verify failed (success=false)', [
                    'status' => \$resp->status(),
                    'session_id' => \$data['sncs'],
                    'error' => \$json['error'] ?? null,
                ]);

                return back()->withInput()->withErrors([
                    'snct' => 'Verifica di sicurezza non superata. Riprova.',
                ]);
            }
        } catch (\\Throwable \$e) {
            Log::error('SN CAPTCHA exception', [
                'message' => \$e->getMessage(),
                'class' => get_class(\$e),
            ]);

            return back()->withInput()->withErrors([
                'snct' => 'Servizio di verifica momentaneamente non disponibile.',
            ]);
        }

        return back()->with('success', 'CAPTCHA valido. Procedi con la logica del form.');
    }
}
PHP);

            $zip->addFromString('blade-snippet.blade.php', <<<BLADE
<form method="POST" action="{{ route('example.store') }}">
    @csrf

    <input type="text" name="name" required>
    <input type="email" name="email" required>

    <div class="sn-captcha" data-sitekey="{{ config('services.sn_captcha.site_key') }}" data-theme="standard"></div>
    <script src="https://captcha.stacknigro.it/widget.js?v=13" async></script>

    @error('snct')
        <div>{{ \$message }}</div>
    @enderror

    <button type="submit">Invia</button>
</form>
BLADE);
        }

        if ($type === 'node') {
            $zip->addFromString('.env.example', <<<ENV
SN_CAPTCHA_SITE_KEY={$siteKey}
SN_CAPTCHA_SECRET={$secret}
SN_CAPTCHA_VERIFY_URL={$verifyUrl}
PORT=3000
ENV);

            $zip->addFromString('server.js', <<<NODE
const express = require('express');
const axios = require('axios');
require('dotenv').config();

const app = express();
app.use(express.urlencoded({ extended: true }));

app.get('/', (req, res) => {
  res.send(\`
    <html>
      <body>
        <form method="POST" action="/submit">
          <input type="text" name="name" placeholder="Nome" required />
          <input type="email" name="email" placeholder="Email" required />
          <div class="sn-captcha" data-sitekey="\${process.env.SN_CAPTCHA_SITE_KEY}" data-theme="standard"></div>
          <script src="https://captcha.stacknigro.it/widget.js?v=13" async></script>
          <button type="submit">Invia</button>
        </form>
      </body>
    </html>
  \`);
});

app.post('/submit', async (req, res) => {
  const token = String(req.body.snct || '').trim();
  const sessionId = String(req.body.sncs || '').trim();

  if (!token || !sessionId) {
    return res.status(422).send('Verifica CAPTCHA mancante.');
  }

  try {
    const params = new URLSearchParams();
    params.append('secret', process.env.SN_CAPTCHA_SECRET || '');
    params.append('token', token);
    params.append('session_id', sessionId);
    params.append('ip', req.headers['x-forwarded-for'] || req.socket.remoteAddress || '');

    const response = await axios.post(
      process.env.SN_CAPTCHA_VERIFY_URL || '{$verifyUrl}',
      params,
      {
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        timeout: 10000
      }
    );

    if (!response.data || response.data.success !== true) {
      return res.status(422).send('CAPTCHA non valido.');
    }

    return res.send('CAPTCHA valido. Procedi con la logica del form.');
  } catch (e) {
    return res.status(500).send('Errore di verifica.');
  }
});

app.listen(process.env.PORT || 3000, () => {
  console.log('Server avviato');
});
NODE);

            $zip->addFromString('package.json', <<<'JSON'
{
  "name": "stacknigro-captcha-example",
  "version": "1.0.0",
  "main": "server.js",
  "scripts": {
    "start": "node server.js"
  },
  "dependencies": {
    "axios": "^1.7.2",
    "dotenv": "^16.4.5",
    "express": "^4.19.2"
  }
}
JSON);
        }

        if ($type === 'serverless') {
            $zip->addFromString('.env.example', <<<ENV
SN_CAPTCHA_SITE_KEY={$siteKey}
SN_CAPTCHA_SECRET={$secret}
SN_CAPTCHA_VERIFY_URL={$verifyUrl}
ENV);

            $zip->addFromString('handler.js', <<<JS
const axios = require('axios');

exports.verifyCaptcha = async (event) => {
  try {
    const body = JSON.parse(event.body || '{}');

    const token = String(body.snct || '').trim();
    const sessionId = String(body.sncs || '').trim();

    if (!token || !sessionId) {
      return {
        statusCode: 422,
        body: JSON.stringify({ success: false, error: 'captcha_missing' })
      };
    }

    const params = new URLSearchParams();
    params.append('secret', process.env.SN_CAPTCHA_SECRET || '');
    params.append('token', token);
    params.append('session_id', sessionId);
    params.append('ip', event.requestContext?.http?.sourceIp || '');

    const response = await axios.post(
      process.env.SN_CAPTCHA_VERIFY_URL || '{$verifyUrl}',
      params,
      {
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        timeout: 10000
      }
    );

    if (!response.data || response.data.success !== true) {
      return {
        statusCode: 422,
        body: JSON.stringify({ success: false, error: 'captcha_invalid' })
      };
    }

    return {
      statusCode: 200,
      body: JSON.stringify({ success: true })
    };
  } catch (e) {
    return {
      statusCode: 500,
      body: JSON.stringify({ success: false, error: 'verify_error' })
    };
  }
};
JS);

            $zip->addFromString('frontend-example.html', <<<HTML
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Serverless Example</title>
</head>
<body>
  <form id="captchaForm">
    <input type="text" name="name" placeholder="Nome" required>
    <input type="email" name="email" placeholder="Email" required>

    <div class="sn-captcha" data-sitekey="{$siteKey}" data-theme="standard"></div>
    <script src="https://captcha.stacknigro.it/widget.js?v=13" async></script>

    <button type="submit">Invia</button>
  </form>
</body>
</html>
HTML);

            $zip->addFromString('package.json', <<<'JSON'
{
  "name": "stacknigro-captcha-serverless-example",
  "version": "1.0.0",
  "dependencies": {
    "axios": "^1.7.2"
  }
}
JSON);
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $uniqueDomain = 'unique:captcha_sites,domain';
        if ($ignoreId) {
            $uniqueDomain .= ',' . $ignoreId . ',id';
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'domain' => ['required', 'string', 'max:190', $uniqueDomain],
            'domains_extra' => ['nullable'],
            'is_active' => ['nullable', 'boolean'],
            'rate_limit_5m' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'rate_limit_day' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function normalizeDomain(string $domain): string
    {
        $domain = trim($domain);
        $domain = preg_replace('#^https?://#i', '', $domain);
        $domain = preg_replace('#/$#', '', $domain);
        return strtolower($domain);
    }
}
