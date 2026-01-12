<?php

namespace MyPlatform\EcommerceCore\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use MyPlatform\EcommerceCore\Modules\Integration\Models\IntegrationConfig;

class IntegrationConfigResource extends Resource
{
    protected static ?string $model = IntegrationConfig::class;
    protected static ?string $navigationLabel = 'Integration Keys';
    protected static ?string $pluralModelLabel = 'API Keys';
    protected static ?string $navigationGroup = 'Settings'; // Create a new group for Settings

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('module')
                    ->options([
                        'payment' => 'Payment Gateway',
                        'shipping' => 'Shipping Provider',
                        'sms' => 'SMS Gateway',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('provider')
                    ->label('Provider Code')
                    ->placeholder('e.g. stripe')
                    ->required(),
                Forms\Components\TextInput::make('key_name')
                    ->label('Key Name')
                    ->placeholder('e.g. secret_key')
                    ->required(),
                Forms\Components\TextInput::make('encrypted_value')
                    ->label('API Secret')
                    ->password()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('module')
                    ->colors(['primary' => 'payment', 'warning' => 'shipping']),
                Tables\Columns\TextColumn::make('provider')->searchable(),
                Tables\Columns\TextColumn::make('key_name')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \MyPlatform\EcommerceCore\Filament\Resources\IntegrationConfigResource\Pages\ListIntegrationConfigs::route('/'),
            'create' => \MyPlatform\EcommerceCore\Filament\Resources\IntegrationConfigResource\Pages\CreateIntegrationConfig::route('/create'),
            'edit' => \MyPlatform\EcommerceCore\Filament\Resources\IntegrationConfigResource\Pages\EditIntegrationConfig::route('/{record}/edit'),
        ];
    }
}
