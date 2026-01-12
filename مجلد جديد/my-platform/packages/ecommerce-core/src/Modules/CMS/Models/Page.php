<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model implements \MyPlatform\EcommerceCore\Contracts\ModuleInterface
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getModuleName(): string
    {
        return 'CMS Pages';
    }

    public static function getModuleFields(): array
    {
        return [
            'id' => 'text',
            'title' => 'text',
            'slug' => 'text',
            'is_active' => 'boolean',
            'updated_at' => 'date',
        ];
    }
}
