<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\ProductResource\Pages;

use MyPlatform\EcommerceCore\Filament\Resources\ProductResource;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;
}
