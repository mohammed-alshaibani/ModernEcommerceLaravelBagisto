<?php

namespace MyPlatform\EcommerceCore\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use MyPlatform\EcommerceCore\Modules\Order\Models\Order;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use MyPlatform\EcommerceCore\Modules\Delivery\Services\ShippingRateService;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationLabel = 'الطلبات';
    protected static ?string $pluralModelLabel = 'الطلبات';
    protected static ?string $navigationGroup = 'المتجر';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'معلق',
                        'paid' => 'مدفوع',
                        'shipped' => 'تم الشحن',
                        'cancelled' => 'ملغي',
                    ]),
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('unit_price')
                            ->numeric()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('رقم الطلب'),
                Tables\Columns\TextColumn::make('user_id')->label('العميل'), // Needs relationship logic
                Tables\Columns\TextColumn::make('total_amount')->label('الإجمالي')->money('SAR'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'primary' => 'shipped',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->actions([
                Action::make('ship')
                    ->label('شحن الطلب')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->action(function (Order $record) {
                        try {
                            // Logic to trigger Shipping Strategy
                            // $service = app(ShippingRateService::class);
                            // $service->ship($record);
                            $record->update(['status' => 'shipped']);
                            Notification::make()->title('Order Shipped')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('Error')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->requiresConfirmation(),
                
                Action::make('refund')
                    ->label('استرجاع المبلغ')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('danger')
                    ->action(function (Order $record) {
                        // Payment Refund Logic
                        $record->update(['status' => 'cancelled']);
                        Notification::make()->title('Order Refunded')->success()->send();
                    })
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \MyPlatform\EcommerceCore\Filament\Resources\OrderResource\Pages\ListOrders::route('/'),
            'create' => \MyPlatform\EcommerceCore\Filament\Resources\OrderResource\Pages\CreateOrder::route('/create'),
            'edit' => \MyPlatform\EcommerceCore\Filament\Resources\OrderResource\Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
