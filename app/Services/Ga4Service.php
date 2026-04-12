<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Ga4Service
{
    private string $credentialsPath;
    private string $propertyId;

    public function __construct()
    {
        $this->credentialsPath = base_path(env('GA_CREDENTIALS'));
        $this->propertyId = (string) env('GA4_PROPERTY_ID');
    }

    private function credentials(): array
    {
        $json = @file_get_contents($this->credentialsPath);
        if ($json === false) {
            throw new \RuntimeException("GA credentials non trovate: {$this->credentialsPath}");
        }
        $data = json_decode($json, true);
        if (!is_array($data) || empty($data['client_email']) || empty($data['private_key'])) {
            throw new \RuntimeException("GA credentials JSON non valido");
        }
        return $data;
    }

    private function base64Url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function accessToken(): string
    {
        $c = $this->credentials();
        $tokenUri = $c['token_uri'] ?? 'https://oauth2.googleapis.com/token';

        $now = time();
        $header = $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = $this->base64Url(json_encode([
            'iss'   => $c['client_email'],
            'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
            'aud'   => $tokenUri,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $unsigned = $header.'.'.$claims;

        $signature = '';
        $ok = openssl_sign($unsigned, $signature, $c['private_key'], OPENSSL_ALGO_SHA256);
        if (!$ok) {
            throw new \RuntimeException("openssl_sign fallita (controlla estensione OpenSSL)");
        }
        $jwt = $unsigned.'.'.$this->base64Url($signature);

        $res = Http::asForm()->post($tokenUri, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        if (!$res->successful()) {
            throw new \RuntimeException("Token OAuth fallito: {$res->status()} ".$res->body());
        }

        $token = $res->json('access_token');
        if (!$token) throw new \RuntimeException("Token assente nella risposta OAuth");
        return $token;
    }

    private function runReport(array $body): array
    {
        if (!$this->propertyId) throw new \RuntimeException("GA4_PROPERTY_ID mancante");

        $token = $this->accessToken();

        $url = "https://analyticsdata.googleapis.com/v1beta/properties/{$this->propertyId}:runReport";

        $res = Http::withToken($token)
            ->acceptJson()
            ->post($url, $body);

        if (!$res->successful()) {
            throw new \RuntimeException("runReport fallito: {$res->status()} ".$res->body());
        }

        return $res->json();
    }

    private function firstMetricValue(array $report): int
    {
        return (int)($report['rows'][0]['metricValues'][0]['value'] ?? 0);
    }

    public function dashboardPayload(): array
    {
        // Visite totali ultimi 30gg (Users)
       logger()->info('GA4 dashboardPayload CALLED');
        $users30 = $this->runReport([
            'dateRanges' => [['startDate' => '30daysAgo', 'endDate' => 'today']],
            'metrics' => [['name' => 'totalUsers']],
        ]);
        $totalUsers30 = $this->firstMetricValue($users30);

        // Pageviews ultimi 30gg
        $pv30 = $this->runReport([
            'dateRanges' => [['startDate' => '30daysAgo', 'endDate' => 'today']],
            'metrics' => [['name' => 'screenPageViews']],
        ]);
        $pageviews30 = $this->firstMetricValue($pv30);

        // Top pagina ultimi 7gg
        $topPage = $this->runReport([
            'dateRanges' => [['startDate' => '7daysAgo', 'endDate' => 'today']],
            'dimensions' => [['name' => 'pagePath']],
            'metrics' => [['name' => 'screenPageViews']],
            'orderBys' => [[
                'metric' => ['metricName' => 'screenPageViews'],
                'desc' => true
            ]],
            'limit' => 1,
        ]);
        $topPagePath = $topPage['rows'][0]['dimensionValues'][0]['value'] ?? '-';
        $topPageViews = (int)($topPage['rows'][0]['metricValues'][0]['value'] ?? 0);

        // Top sorgente ultimi 7gg
        $topSource = $this->runReport([
            'dateRanges' => [['startDate' => '7daysAgo', 'endDate' => 'today']],
            'dimensions' => [['name' => 'sessionSourceMedium']],
            'metrics' => [['name' => 'sessions']],
            'orderBys' => [[
                'metric' => ['metricName' => 'sessions'],
                'desc' => true
            ]],
            'limit' => 1,
        ]);
        $topSourceName = $topSource['rows'][0]['dimensionValues'][0]['value'] ?? '-';
        $topSourceSessions = (int)($topSource['rows'][0]['metricValues'][0]['value'] ?? 0);

        // Device ultimi 7gg
$topDevice = $this->runReport([
    'dateRanges' => [['startDate' => '7daysAgo', 'endDate' => 'today']],
    'dimensions' => [['name' => 'deviceCategory']],
    'metrics' => [['name' => 'sessions']],
    'orderBys' => [[
        'metric' => ['metricName' => 'sessions'],
        'desc' => true
    ]],
    'limit' => 1,
]);

$device = $topDevice['rows'][0]['dimensionValues'][0]['value'] ?? '-';
$deviceSessions = (int)($topDevice['rows'][0]['metricValues'][0]['value'] ?? 0);

        // Grafico users ultimi 7gg (date + totalUsers)
        $users7 = $this->runReport([
            'dateRanges' => [['startDate' => '7daysAgo', 'endDate' => 'today']],
            'dimensions' => [['name' => 'date']],
            'metrics' => [['name' => 'totalUsers']],
            'orderBys' => [[
                'dimension' => ['dimensionName' => 'date'],
                'desc' => false
            ]],
        ]);

        $users7Series = [];
        foreach (($users7['rows'] ?? []) as $row) {
            $users7Series[] = [
                'date' => $row['dimensionValues'][0]['value'] ?? '',
                'value' => (int)($row['metricValues'][0]['value'] ?? 0),
            ];
        }

        //Grafico users ultimi 30gg (date + totalUsers)
$users30 = $this->runReport([
    'dateRanges' => [['startDate' => '30daysAgo', 'endDate' => 'today']],
    'dimensions' => [['name' => 'date']],
    'metrics' => [['name' => 'totalUsers']],
    'orderBys' => [[
        'dimension' => ['dimensionName' => 'date'],
        'desc' => false
    ]],
]);

$users30Series = [];
foreach (($users30['rows'] ?? []) as $row) {
    $users30Series[] = [
        'date' => $row['dimensionValues'][0]['value'] ?? '',
        'value' => (int)($row['metricValues'][0]['value'] ?? 0),
    ];
}

// Grafico pageviews ultimi 30gg
$pv30 = $this->runReport([
    'dateRanges' => [['startDate' => '30daysAgo', 'endDate' => 'today']],
    'dimensions' => [['name' => 'date']],
    'metrics' => [['name' => 'screenPageViews']],
    'orderBys' => [[
        'dimension' => ['dimensionName' => 'date'],
        'desc' => false
    ]],
]);

$pv30Series = [];
foreach (($pv30['rows'] ?? []) as $row) {
    $pv30Series[] = [
        'date' => $row['dimensionValues'][0]['value'] ?? '',
        'value' => (int)($row['metricValues'][0]['value'] ?? 0),
    ];
}

        // Grafico pageviews ultimi 7gg (date + screenPageViews)
        $pv7 = $this->runReport([
            'dateRanges' => [['startDate' => '7daysAgo', 'endDate' => 'today']],
            'dimensions' => [['name' => 'date']],
            'metrics' => [['name' => 'screenPageViews']],
            'orderBys' => [[
                'dimension' => ['dimensionName' => 'date'],
                'desc' => false
            ]],
        ]);

        $pv7Series = [];
        foreach (($pv7['rows'] ?? []) as $row) {
            $pv7Series[] = [
                'date' => $row['dimensionValues'][0]['value'] ?? '',
                'value' => (int)($row['metricValues'][0]['value'] ?? 0),
            ];
        }

        return [
            'total_users_30' => $totalUsers30,
            'pageviews_30' => $pageviews30,
            'top_page' => ['path' => $topPagePath, 'views' => $topPageViews],
            'top_source' => ['name' => $topSourceName, 'sessions' => $topSourceSessions],
            'top_device' => [
    'device' => $device,
    'sessions' => $deviceSessions,
],
            'series_users_7' => $users7Series,
            'series_pageviews_7' => $pv7Series,
            'series_users_30' => $users30Series,
'series_pageviews_30' => $pv30Series,
        ];
    }
}
