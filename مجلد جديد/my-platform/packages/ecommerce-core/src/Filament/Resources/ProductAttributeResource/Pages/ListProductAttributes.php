<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\ProductAttributeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use MyPlatform\EcommerceCore\Filament\Resources\ProductAttributeResource;

class ListProductAttributes extends ListRecords
{
    protected static string $resource = ProductAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
