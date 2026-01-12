<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\ProductCreated;
use App\Notifications\NewProductNotification;

class NotifyVendorOfNewProduct implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */

    public function handle(ProductCreated $event)
    {
        $vendor = $event->product->vendor;
        $vendor->user->notify(new NewProductNotification($event->product)); // Assuming a User model exists
    }

   
}
