<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\OrderResource\Pages;

use MyPlatform\EcommerceCore\Filament\Resources\OrderResource;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;
}
