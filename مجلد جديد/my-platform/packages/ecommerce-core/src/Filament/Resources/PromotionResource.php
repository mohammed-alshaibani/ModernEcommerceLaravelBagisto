<?php

namespace MyPlatform\EcommerceCore\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use MyPlatform\EcommerceCore\Modules\Product\Models\Promotion;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;
    protected static ?string $navigationLabel = 'الخصومات';
    protected static ?string $pluralModelLabel = 'الخصومات والعروض';
    protected static ?string $navigationGroup = 'التسويق';
    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('تفاصيل الخصم')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('اسم العرض')
                                    ->required(),
                                Forms\Components\TextInput::make('code')
                                    ->label('كود الخصم')
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->extraInputAttributes(['style' => 'text-transform:uppercase']),
                                Forms\Components\Select::make('type')
                                    ->label('نوع الخصم')
                                    ->options([
                                        'percentage' => 'نسبة مئوية (%)',
                                        'fixed_amount' => 'مبلغ ثابت',
                                    ])
                                    ->default('percentage')
                                    ->required(),
                                Forms\Components\TextInput::make('value')
                                    ->label('قيمة الخصم')
                                    ->numeric()
                                    ->required(),
                            ])->columns(2),
                        
                        Forms\Components\Section::make('الشروط والقيود')
                            ->schema([
                                Forms\Components\TextInput::make('min_purchase_amount')
                                    ->label('الحد الأدنى للشراء')
                                    ->numeric()
                                    ->prefix('SAR'),
                                Forms\Components\TextInput::make('max_uses')
                                    ->label('الحد الأقصى للاستخدام')
                                    ->numeric()
                                    ->helperText('أتركه فارغاً للاستخدام غير المحدود'),
                                Forms\Components\Select::make('applies_to')
                                    ->label('ينطبق على')
                                    ->options([
                                        'all' => 'كل المنتجات',
                                        'products' => 'منتجات محددة',
                                        'categories' => 'أقسام محددة',
                                    ])
                                    ->default('all')
                                    ->live(),
                                Forms\Components\Select::make('products')
                                    ->label('اختر المنتجات')
                                    ->relationship('products', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (Forms\Get $get) => $get('applies_to') === 'products'),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('الحالة والتاريخ')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('مفعل')
                                    ->default(true),
                                Forms\Components\DateTimePicker::make('starts_at')
                                    ->label('تاريخ البدء'),
                                Forms\Components\DateTimePicker::make('expires_at')
                                    ->label('تاريخ الانتهاء'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('اسم العرض')->searchable(),
                Tables\Columns\TextColumn::make('code')->label('الكود')->badge()->color('success'),
                Tables\Columns\TextColumn::make('value')
                    ->label('الخصم')
                    ->formatStateUsing(fn ($record) => $record->type === 'percentage' ? "{$record->value}%" : "{$record->value} SAR"),
                Tables\Columns\TextColumn::make('uses_count')->label('مرات الاستخدام')->counts('products'), 
                Tables\Columns\IconColumn::make('is_active')->label('الحالة')->boolean(),
                Tables\Columns\TextColumn::make('expires_at')->label('ينتهي في')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('الحالة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \MyPlatform\EcommerceCore\Filament\Resources\PromotionResource\Pages\ListPromotions::route('/'),
            'create' => \MyPlatform\EcommerceCore\Filament\Resources\PromotionResource\Pages\CreatePromotion::route('/create'),
            'edit' => \MyPlatform\EcommerceCore\Filament\Resources\PromotionResource\Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
