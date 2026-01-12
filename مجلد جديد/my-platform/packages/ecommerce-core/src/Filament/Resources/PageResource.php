<?php

namespace MyPlatform\EcommerceCore\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use MyPlatform\EcommerceCore\Modules\CMS\Models\Page;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationLabel = 'الصفحات';
    protected static ?string $pluralModelLabel = 'صفحات المحتوى';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 2;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('المحتوى')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('العنوان')
                                    ->required()
                                    ->lazy()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->label('الرابط المختصر (Slug)')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Forms\Components\RichEditor::make('content')
                                    ->label('المحتوى')
                                    ->required()
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('الإعدادات')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('نشطة')
                                    ->default(true),
                            ]),
                        Forms\Components\Section::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('عنوان SEO'),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('وصف SEO'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('الرابط'),
                Tables\Columns\IconColumn::make('is_active')->label('الحالة')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('آخر تحديث')->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \MyPlatform\EcommerceCore\Filament\Resources\PageResource\Pages\ListPages::route('/'),
            'create' => \MyPlatform\EcommerceCore\Filament\Resources\PageResource\Pages\CreatePage::route('/create'),
            'edit' => \MyPlatform\EcommerceCore\Filament\Resources\PageResource\Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
