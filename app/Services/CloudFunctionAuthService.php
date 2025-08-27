<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudFunctionAuthService
{
    /**
     * Get cached identity token with proper server-side caching
     * Uses Laravel Cache (Redis/File) - NOT session storage
     */
    public function getCachedIdentityToken($targetAudience)
    {
        $cacheKey = 'identity_token';
        
        try {
            if (Cache::has($cacheKey)) {
                $cachedData = Cache::get($cacheKey);
                
                if (isset($cachedData['expires_at']) && $cachedData['expires_at'] > time() + 300) {
                    Log::info("Using cached identity token");
                    return $cachedData['token'];
                } else {
                    Log::info("Cached token expired, generating new one");
                    $this->clearIdentityTokenCache();
                }
            }
            
            $identityToken = $this->generateIdentityToken(
                $this->getCredentialsPath(), 
                $targetAudience
            );
            
            Cache::put($cacheKey, [
                'token' => $identityToken,
                'expires_at' => time() + 3000, // 50 minutes
                'created_at' => time()
            ], 50);
            
            Log::info("New identity token generated and cached for 50 minutes");
            return $identityToken;
            
        } catch (Exception $e) {
            Log::error("Failed to get cached identity token: " . $e->getMessage());
            $this->clearIdentityTokenCache();
            throw $e;
        }
    }
    
    /**
     * Clear identity token cache on error
     */
    public function clearIdentityTokenCache()
    {
        $cacheKey = 'identity_token';
        Cache::forget($cacheKey);
        Log::info("Identity token cache cleared");
    }
    
    /**
     * Get credentials file path using same logic as original implementation
     */
    private function getCredentialsPath()
    {
        return base_path(config('app.firebase.credentials'));
    }
   
    /**
     * Generate identity token for Cloud Function authentication
     */
    private function generateIdentityToken($credentialsPath, $targetAudience)
    {
        try {
            $credentialsData = json_decode(file_get_contents($credentialsPath), true);
            
            if (!$credentialsData || !isset($credentialsData['private_key']) || !isset($credentialsData['client_email'])) {
                throw new Exception('Invalid service account credentials file');
            }

            $now = time();
            $payload = [
                'iss' => $credentialsData['client_email'],        // Issuer
                'sub' => $credentialsData['client_email'],        // Subject
                'aud' => 'https://oauth2.googleapis.com/token',   // Google OAuth2 endpoint
                'iat' => $now,                                    // Issued at time
                'exp' => $now + 3600,                             // Expiration (1 hour)
                'target_audience' => $targetAudience              // Your Cloud Run URL
            ];

            $jwt = $this->createSignedJWT($payload, $credentialsData['private_key']);

            $response = Http::timeout(10)->asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if (!$response->successful()) {
                throw new Exception('Failed to exchange JWT for identity token: ' . $response->body());
            }

            $tokenData = $response->json();
            
            if (!isset($tokenData['id_token'])) {
                throw new Exception('Identity token not found in OAuth response');
            }

            return $tokenData['id_token'];
            
        } catch (Exception $e) {
            throw new Exception('Identity token generation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Create and sign JWT using RS256 algorithm
     */
    private function createSignedJWT($payload, $privateKey)
    {
        try {
            $header = ['typ' => 'JWT', 'alg' => 'RS256'];

            $headerEncoded = $this->base64UrlEncode(json_encode($header));
            $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
            
            $dataToSign = $headerEncoded . '.' . $payloadEncoded;
            $signature = '';
            
            if (!openssl_sign($dataToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                throw new Exception('Failed to sign JWT: ' . openssl_error_string());
            }
            
            $signatureEncoded = $this->base64UrlEncode($signature);
            
            return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
            
        } catch (Exception $e) {
            throw new Exception('JWT creation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Base64 URL-safe encoding
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}