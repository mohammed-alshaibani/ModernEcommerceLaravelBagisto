<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\CustomerResource\Pages;

use MyPlatform\EcommerceCore\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
