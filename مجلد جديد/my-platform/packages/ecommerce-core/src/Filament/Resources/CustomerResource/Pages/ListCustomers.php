<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\CustomerResource\Pages;

use MyPlatform\EcommerceCore\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;
}
