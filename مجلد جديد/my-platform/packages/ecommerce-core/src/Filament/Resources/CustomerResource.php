<?php

namespace MyPlatform\EcommerceCore\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use Illuminate\Foundation\Auth\User; // Using standard User model for customers

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'العملاء';
    protected static ?string $pluralModelLabel = 'العملاء';
    protected static ?string $navigationGroup = 'المتجر';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label('كلمة المرور')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                
                Forms\Components\Section::make('الولاء والعضوية')
                    ->schema([
                        Forms\Components\TextInput::make('loyalty.points_balance')
                            ->label('رصيد النقاط')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('loyalty.membership_level_id')
                            ->label('مستوى العضوية')
                            ->options(\MyPlatform\EcommerceCore\Modules\Customer\Models\MembershipLevel::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('البريد الإلكتروني')->searchable(),
                Tables\Columns\TextColumn::make('loyalty.points_balance')
                    ->label('نقاط الولاء')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('loyalty.membershipLevel.name')
                    ->label('مستوى العضوية')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ التسجيل')->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \MyPlatform\EcommerceCore\Filament\Resources\CustomerResource\Pages\ListCustomers::route('/'),
            'create' => \MyPlatform\EcommerceCore\Filament\Resources\CustomerResource\Pages\CreateCustomer::route('/create'),
            'edit' => \MyPlatform\EcommerceCore\Filament\Resources\CustomerResource\Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
