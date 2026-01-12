<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\ProductAttributeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use MyPlatform\EcommerceCore\Filament\Resources\ProductAttributeResource;

class EditProductAttribute extends EditRecord
{
    protected static string $resource = ProductAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
