<?php

namespace MyPlatform\EcommerceCore\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use MyPlatform\EcommerceCore\Services\ModuleManager;
use Illuminate\Support\Str;

class ManageModules extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Module Manager';
    protected static ?string $slug = 'module-manager';
    
    protected static string $view = 'ecommerce-core::filament.pages.manage-modules';

    public $modules = [];
    public ?string $activeModule = null;

    public function mount(ModuleManager $moduleManager)
    {
        $this->modules = $moduleManager->getRegisteredModules();
        $this->activeModule = request('module');
    }

    public function table(Table $table): Table
    {
        if (!$this->activeModule) {
            return $table->query(\MyPlatform\EcommerceCore\Modules\Order\Models\Order::query()->whereRaw('1=0')); // Empty query
        }

        $modelClass = urldecode($this->activeModule);
        
        if (!class_exists($modelClass)) {
            return $table;
        }

        $schema = $modelClass::getModuleFields();
        $columns = [];

        foreach ($schema as $field => $type) {
            $column = TextColumn::make($field)->sortable();

            if (Str::startsWith($type, 'money')) {
                $column->money('SAR');
            } elseif (Str::startsWith($type, 'date')) {
                $column->date();
            } elseif (Str::startsWith($type, 'select')) {
                $column->badge();
            }

            $columns[] = $column;
        }

        return $table
            ->query($modelClass::query())
            ->columns($columns)
            ->heading($modelClass::getModuleName() . ' Data');
    }
}
