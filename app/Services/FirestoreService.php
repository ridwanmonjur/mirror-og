<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Services\CloudFunctionAuthService;

class FirestoreService
{
    /**
     * @var CloudFunctionAuthService
     */
    protected $authService;

    /**
     * Constructor to inject CloudFunctionAuthService dependency
     */
    public function __construct(CloudFunctionAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get match result from Firestore via Cloud Function
     *
     * @param string|int $eventId Event ID
     * @param string $matchId Match ID (e.g. "P1.P2")
     * @return array|null Match data or null if not found
     */
    public function getMatchResult(string|int $eventId, string $matchId): ?array
    {
        try {
            $cloudFunctionUrl = config('services.cloud_server_functions.url');

            // Get cached identity token for authentication
            $identityToken = $this->authService->getCachedIdentityToken($cloudFunctionUrl);

            $response = Http::timeout(30)
                ->contentType('application/json')
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $identityToken,
                    'User-Agent' => 'Laravel-App/1.0'
                ])
                ->post($cloudFunctionUrl . '/match/result', [
                    'event_id' => $eventId,
                    'match_id' => $matchId,
                ]);

            if (!$response->successful()) {
                // Clear cache on authentication errors and retry once
                if ($response->status() === 401 || $response->status() === 403) {
                    \Illuminate\Support\Facades\Log::warning("Authentication failed for getMatchResult, clearing token cache and retrying");
                    $this->authService->clearIdentityTokenCache($cloudFunctionUrl);

                    // Retry once with fresh token
                    $identityToken = $this->authService->getCachedIdentityToken($cloudFunctionUrl);
                    $response = Http::timeout(30)
                        ->contentType('application/json')
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . $identityToken,
                            'User-Agent' => 'Laravel-App/1.0'
                        ])
                        ->post($cloudFunctionUrl . '/match/result', [
                            'event_id' => $eventId,
                            'match_id' => $matchId,
                        ]);
                }

                if (!$response->successful()) {
                    error_log('FirestoreService getMatchResult cloud function call failed: Status ' . $response->status() . ', Body: ' . $response->body());
                    return null;
                }
            }

            $responseData = $response->json();

            if ($responseData['status'] === 'not_found') {
                return null;
            }

            if ($responseData['status'] !== 'success') {
                error_log('FirestoreService getMatchResult error: ' . ($responseData['message'] ?? 'Unknown error'));
                return null;
            }

            return $responseData['data'] ?? null;
        } catch (\Exception $e) {
            error_log('FirestoreService getMatchResult error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all match results for an event from Firestore via Cloud Function
     *
     * @param string|int $eventId Event ID
     * @return array Array of match results keyed by match ID
     */
    public function getAllMatchResults(string|int $eventId): array
    {
        try {
            $cloudFunctionUrl = config('services.cloud_server_functions.url');

            // Get cached identity token for authentication
            $identityToken = $this->authService->getCachedIdentityToken($cloudFunctionUrl);

            $response = Http::timeout(30)
                ->contentType('application/json')
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $identityToken,
                    'User-Agent' => 'Laravel-App/1.0'
                ])
                ->post($cloudFunctionUrl . '/match/results/all', [
                    'event_id' => $eventId,
                ]);

            if (!$response->successful()) {
                // Clear cache on authentication errors and retry once
                if ($response->status() === 401 || $response->status() === 403) {
                    \Illuminate\Support\Facades\Log::warning("Authentication failed for getAllMatchResults, clearing token cache and retrying");
                    $this->authService->clearIdentityTokenCache($cloudFunctionUrl);

                    // Retry once with fresh token
                    $identityToken = $this->authService->getCachedIdentityToken($cloudFunctionUrl);
                    $response = Http::timeout(30)
                        ->contentType('application/json')
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . $identityToken,
                            'User-Agent' => 'Laravel-App/1.0'
                        ])
                        ->post($cloudFunctionUrl . '/match/results/all', [
                            'event_id' => $eventId,
                        ]);
                }

                if (!$response->successful()) {
                    error_log('FirestoreService getAllMatchResults cloud function call failed: Status ' . $response->status() . ', Body: ' . $response->body());
                    return [];
                }
            }

            $responseData = $response->json();

            if ($responseData['status'] !== 'success') {
                error_log('FirestoreService getAllMatchResults error: ' . ($responseData['message'] ?? 'Unknown error'));
                return [];
            }

            return $responseData['data'] ?? [];
        } catch (\Exception $e) {
            error_log('FirestoreService getAllMatchResults error: ' . $e->getMessage());
            return [];
        }
    }

}
