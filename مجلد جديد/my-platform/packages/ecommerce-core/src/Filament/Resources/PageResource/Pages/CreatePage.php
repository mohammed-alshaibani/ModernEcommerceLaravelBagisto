<?php

namespace MyPlatform\EcommerceCore\Filament\Resources\PageResource\Pages;

use MyPlatform\EcommerceCore\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
}
