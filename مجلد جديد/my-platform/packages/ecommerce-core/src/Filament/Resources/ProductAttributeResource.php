<?php

namespace MyPlatform\EcommerceCore\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use MyPlatform\EcommerceCore\Filament\Resources\ProductAttributeResource\Pages;
use MyPlatform\EcommerceCore\Modules\Product\Models\ProductAttribute;

class ProductAttributeResource extends Resource
{
    protected static ?string $model = ProductAttribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    
    protected static ?string $navigationLabel = 'صفات المنتج (Attributes)';
    protected static ?string $pluralModelLabel = 'صفات المنتج';
    protected static ?string $modelLabel = 'صفة';

    protected static ?string $navigationGroup = 'المتجر';
    protected static ?int $navigationSort = 2; // After Products

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الصفة')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم (مثال: اللون)')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->label('الاسم اللطيف (Slug)')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('type')
                            ->label('طريقة العرض')
                            ->options([
                                'select' => 'قائمة منسدلة (Select)',
                                'radio' => 'خيار واحد (Radio)',
                                'color' => 'لون (Color)',
                                'button' => 'أزرار (Buttons)',
                            ])
                            ->default('select')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('القيم المتاحة')
                    ->schema([
                        Repeater::make('values')
                            ->relationship('values')
                            ->schema([
                                TextInput::make('value')
                                    ->label('القيمة (مثال: أحمر)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' || empty($set('slug')) ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required(),
                                TextInput::make('meta_value')
                                    ->label('قيمة إضافية (Hex Color)')
                                    ->nullable(),
                            ])
                            ->columns(3)
                            ->orderColumn('position')
                            ->defaultItems(1)
                            ->addActionLabel('إضافة قيمة جديدة'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug'),
                Tables\Columns\TextColumn::make('type')->label('النوع')->badge(),
                Tables\Columns\TextColumn::make('values_count')->counts('values')->label('عدد القيم'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductAttributes::route('/'),
            'create' => Pages\CreateProductAttribute::route('/create'),
            'edit' => Pages\EditProductAttribute::route('/{record}/edit'),
        ];
    }
}
