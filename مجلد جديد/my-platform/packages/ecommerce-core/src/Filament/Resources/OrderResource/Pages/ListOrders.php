<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\OrderResource\Pages;

use MyPlatform\EcommerceCore\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;
}
