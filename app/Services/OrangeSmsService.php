<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrangeSmsService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;
    protected $accessToken;

    public function __construct()
    {
        $this->clientId = config('services.orange.client_id');
        $this->clientSecret = config('services.orange.client_secret');
        $this->baseUrl = 'https://api.orange.com';
        $this->accessToken = null;
    }

    /**
     * Obtenir un token d'accès
     */
    protected function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $credentials = base64_encode($this->clientId . ':' . $this->clientSecret);

            Log::info('Tentative obtention token Orange SMS', [
                'client_id' => $this->clientId,
                'base_url' => $this->baseUrl,
                'endpoint' => "{$this->baseUrl}/oauth/v3/token"
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json'
            ])->withBody('grant_type=client_credentials', 'application/x-www-form-urlencoded')
              ->post("{$this->baseUrl}/oauth/v3/token");

            Log::info('Réponse token Orange SMS', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                Log::info('Token Orange SMS obtenu avec succès');
                return $this->accessToken;
            }

            Log::error('Erreur obtention token Orange SMS', [
                'status' => $response->status(),
                'response' => $response->body(),
                'headers' => $response->headers()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception lors de l\'obtention du token Orange SMS', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Envoyer un SMS
     */
    public function sendSms(string $phoneNumber, string $message): bool
    {
        $token = $this->getAccessToken();

        if (!$token) {
            Log::error('Impossible d\'obtenir le token d\'accès pour Orange SMS');
            return false;
        }

        try {
            // Formater le numéro de téléphone (enlever le + si présent)
            $formattedPhone = ltrim($phoneNumber, '+');

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post("{$this->baseUrl}/smsmessaging/v1/outbound/tel:+221317583/requests", [
                'outboundSMSMessageRequest' => [
                    'address' => "tel:+{$formattedPhone}",
                    'senderAddress' => 'tel:+221317583',
                    'outboundSMSTextMessage' => [
                        'message' => $message
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info('SMS envoyé avec succès', [
                    'phone' => $phoneNumber,
                    'message' => substr($message, 0, 50) . '...'
                ]);
                return true;
            }

            Log::error('Erreur envoi SMS Orange', [
                'phone' => $phoneNumber,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Exception lors de l\'envoi du SMS', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}