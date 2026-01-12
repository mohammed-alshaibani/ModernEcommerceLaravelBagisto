<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Analytics\Services;

use MyPlatform\EcommerceCore\Modules\Analytics\Models\Visit;
use MyPlatform\EcommerceCore\Modules\Analytics\Models\AnalyticEvent;
use MyPlatform\EcommerceCore\Modules\Analytics\Models\Conversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AnalyticsService
{
    /**
     * Track a page visit
     */
    public function trackVisit(Request $request): void
    {
        Visit::create([
            'user_id' => $request->user()?->id,
            'session_id' => Session::getId(),
            'ip_address' => $request->ip(),
            'url' => $request->fullUrl(),
            'referer' => $request->headers->get('referer'),
            'user_agent' => $request->userAgent(),
            'device_type' => $this->detectDevice($request->userAgent() ?? ''),
        ]);
    }

    /**
     * Track a custom event
     */
    public function trackEvent(string $eventName, array $data = []): void
    {
        AnalyticEvent::create([
            'session_id' => Session::getId(),
            'user_id' => auth()->id(),
            'event_name' => $eventName,
            'event_data' => $data,
        ]);
    }

    /**
     * Track a conversion (order)
     */
    public function trackConversion($order, ?string $source = null): void
    {
        Conversion::create([
            'session_id' => Session::getId(),
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'source' => $source ?? $this->getRefererSource(),
        ]);
    }

    protected function detectDevice(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);
        if (str_contains($userAgent, 'mobile')) return 'mobile';
        if (str_contains($userAgent, 'tablet')) return 'tablet';
        return 'desktop';
    }

    protected function getRefererSource(): string
    {
        $referer = request()->headers->get('referer');
        if (!$referer) return 'direct';
        
        if (str_contains($referer, 'google.com')) return 'google';
        if (str_contains($referer, 'facebook.com')) return 'social';
        
        return 'other';
    }
}
