<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\PromotionResource\Pages;

use MyPlatform\EcommerceCore\Filament\Resources\PromotionResource;
use Filament\Resources\Pages\EditRecord;

class EditPromotion extends EditRecord
{
    protected static string $resource = PromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
