<?php

namespace MyPlatform\EcommerceCore\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use MyPlatform\EcommerceCore\Modules\Product\Models\Product;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationLabel = 'المنتجات'; // Arabic Label
    protected static ?string $pluralModelLabel = 'المنتجات'; // Arabic Label
    
    // Cluster: Shop (المتجر)
    // protected static ?string $cluster = ShopCluster::class; // Need to create cluster first if used

    protected static ?string $navigationGroup = 'المتجر'; // Grouping for now

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('معلومات المنتج')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('اسم المنتج')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('type')
                                    ->label('نوع المنتج')
                                    ->options([
                                        'simple' => 'منتج بسيط (Simple)',
                                        'variable' => 'منتج بمتغيرات (Variable)',
                                    ])
                                    ->default('simple')
                                    ->required()
                                    ->live(), // Important for reactivity
                                Forms\Components\Textarea::make('description')
                                    ->label('الوصف')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make('البيانات الأساسية')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('السعر')
                                    ->numeric()
                                    ->prefix('SAR')
                                    ->required(fn (Forms\Get $get) => $get('type') !== 'variable'),
                                Forms\Components\TextInput::make('sku')
                                    ->label('رمز المنتج (SKU)')
                                    ->unique(ignoreRecord: true)
                                    ->required(fn (Forms\Get $get) => $get('type') !== 'variable'),
                                Forms\Components\TextInput::make('stock')
                                    ->label('الكمية المتوفرة')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(2)
                            ->hidden(fn (Forms\Get $get) => $get('type') === 'variable'), // Hide for variable products

                        Forms\Components\Section::make('المتغيرات (Variants)')
                            ->schema([
                                Forms\Components\Repeater::make('variants')
                                    ->relationship('variants')
                                    ->schema([
                                        Forms\Components\TextInput::make('sku')
                                            ->label('SKU')
                                            ->required(),
                                        Forms\Components\TextInput::make('price')
                                            ->label('السعر')
                                            ->numeric()
                                            ->prefix('SAR')
                                            ->required(),
                                        Forms\Components\TextInput::make('stock')
                                            ->label('الكمية')
                                            ->numeric()
                                            ->default(0),
                                        Forms\Components\KeyValue::make('options')
                                            ->label('الخصائص (Color: Red)')
                                            ->keyLabel('الخاصية (مثال: Color)')
                                            ->valueLabel('القيمة (مثال: Red)')
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1)
                                    ->addActionLabel('إضافة متغير جديد'),
                            ])
                            ->hidden(fn (Forms\Get $get) => $get('type') !== 'variable'),

                        Forms\Components\Section::make('الوسائط (Media)')
                            ->schema([
                                Forms\Components\Repeater::make('media')
                                    ->relationship('media')
                                    ->schema([
                                        Forms\Components\FileUpload::make('path')
                                            ->label('الملف')
                                            ->image() // Or general file if video
                                            ->directory('products')
                                            ->required(),
                                        Forms\Components\Select::make('type')
                                            ->options([
                                                'image' => 'صورة',
                                                'video' => 'فيديو',
                                            ])
                                            ->default('image'),
                                        Forms\Components\TextInput::make('sort_order')
                                            ->numeric()
                                            ->default(0),
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('صورة رئيسية'),
                                    ])
                                    ->columns(2)
                                    ->orderColumn('sort_order')
                                    ->defaultItems(0)
                                    ->addActionLabel('إضافة وسائط'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('حالة النشر')
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('تاريخ الإنشاء')
                                    ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? '-'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('اسم المنتج')->searchable(),
                Tables\Columns\TextColumn::make('sku')->label('SKU')->searchable(),
                Tables\Columns\TextColumn::make('price')->label('السعر')->money('SAR'),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإضافة')->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \MyPlatform\EcommerceCore\Filament\Resources\ProductResource\Pages\ListProducts::route('/'),
            'create' => \MyPlatform\EcommerceCore\Filament\Resources\ProductResource\Pages\CreateProduct::route('/create'),
            'edit' => \MyPlatform\EcommerceCore\Filament\Resources\ProductResource\Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
