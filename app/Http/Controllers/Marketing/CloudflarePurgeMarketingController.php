<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;

final class CloudflarePurgeMarketingController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $ts  = (string) $request->query('ts', '');
        $sig = (string) $request->query('sig', '');

        if (!$this->isValidSignature($ts, $sig)) {
            return response('Unauthorized', 401);
        }

        $middleware = (string) $request->query('middleware', 'marketing');
        $appUrl = rtrim((string) config('app.url'), '/');

        $urls = collect(Route::getRoutes())
            ->filter(fn($route) => in_array('GET', $route->methods(), true) || in_array('HEAD', $route->methods(), true))
            ->filter(fn($route) => in_array($middleware, $route->gatherMiddleware(), true))
            ->map(fn($route) => $route->uri())
            ->filter(fn($uri) => !Str::contains($uri, '{')) // safety
            ->map(fn($uri) => $uri === '/' ? $appUrl . '/' : $appUrl . '/' . ltrim($uri, '/'))
            ->unique()
            ->values();

        if ($urls->isEmpty()) {
            return response()->json(['ok' => true, 'purged' => 0, 'urls' => []]);
        }

        $zoneId = config('services.cloudflare.zone_id');
        $token  = config('services.cloudflare.api_token');

        $chunkSize = (int) config('services.cloudflare.purge_chunk', 30);

        $purged = 0;
        foreach ($urls->chunk($chunkSize) as $chunk) {
            $res = Http::withToken($token)->post(
                "https://api.cloudflare.com/client/v4/zones/{$zoneId}/purge_cache",
                ['files' => $chunk->all()]
            );

            if (!$res->ok() || $res->json('success') !== true) {
                return response()->json([
                    'ok' => false,
                    'status' => $res->status(),
                    'body' => $res->json(),
                ], 500);
            }

            $purged += $chunk->count();
        }

        return response()->json([
            'ok' => true,
            'purged' => $purged,
            'urls' => $urls, // optional; remove if you don't want to expose
        ]);
    }

    private function isValidSignature(string $ts, string $sig): bool
    {
        if ($ts === '' || $sig === '') return false;
        if (abs(time() - (int) $ts) > 300) return false;

        $secret = (string) config('services.cloudflare.purge_secret');
        $expected = hash_hmac('sha256', $ts, $secret);

        return hash_equals($expected, $sig);
    }
}
