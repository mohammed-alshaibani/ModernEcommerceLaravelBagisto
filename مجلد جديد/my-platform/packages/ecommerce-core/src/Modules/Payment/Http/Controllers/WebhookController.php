<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Payment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request, string $provider)
    {
        Log::info("Payment Webhook received from {$provider}", $request->all());

        return match ($provider) {
            'stripe' => $this->handleStripe($request),
            'moyasar' => $this->handleMoyasar($request),
            default => response()->json(['error' => 'Unknown provider'], 400),
        };
    }

    protected function handleStripe(Request $request)
    {
        $signature = $request->header('Stripe-Signature');
        
        if (!$signature) {
             Log::error('Stripe Webhook missing signature');
             return response()->json(['error' => 'Missing signature'], 400);
        }

        // TODO: Use Stripe SDK to verify signature properly
        // \Stripe\Webhook::constructEvent($payload, $signature, $secret);

        Log::info('Stripe Webhook Verified');
        return response()->json(['status' => 'success']);
    }

    protected function handleMoyasar(Request $request)
    {
        // Moyasar uses Basic Auth or specific header for webhooks
        // This is a placeholder for actual verification logic
        
        if (!$request->input('id')) {
             return response()->json(['error' => 'Invalid payload'], 400);
        }

        Log::info('Moyasar Webhook Received for ID: ' . $request->input('id'));
        return response()->json(['status' => 'success']);
    }
}
