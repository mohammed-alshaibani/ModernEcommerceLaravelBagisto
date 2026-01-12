<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Listeners;

use MyPlatform\EcommerceCore\Events\PaymentSuccessful;
use Filament\Notifications\Notification;
use App\Models\User;

class NotifyAdminOfLargeOrder
{
    public function handle(PaymentSuccessful $event): void
    {
        $order = $event->order;

        if ($order->total_amount > 1000) {
            // Find admin users
            $admins = User::where('email', 'admin@example.com')->get(); // Simplification

            Notification::make()
                ->title('Large Order Received!')
                ->body("Order #{$order->id} for SAR {$order->total_amount} was just paid.")
                ->success()
                ->sendToDatabase($admins);
        }
    }
}
