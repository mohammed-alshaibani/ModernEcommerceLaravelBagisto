<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\PromotionResource\Pages;

use MyPlatform\EcommerceCore\Filament\Resources\PromotionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListPromotions extends ListRecords
{
    protected static string $resource = PromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
