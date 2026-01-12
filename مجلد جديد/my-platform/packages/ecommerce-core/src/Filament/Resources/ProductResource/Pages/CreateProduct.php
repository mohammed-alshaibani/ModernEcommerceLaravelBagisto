<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\ProductResource\Pages;

use MyPlatform\EcommerceCore\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
