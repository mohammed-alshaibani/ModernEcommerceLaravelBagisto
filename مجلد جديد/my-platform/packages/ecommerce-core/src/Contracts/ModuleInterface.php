<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Contracts;

interface ModuleInterface
{
    /**
     * Get the human-readable name of the module.
     */
    public static function getModuleName(): string;

    /**
     * Get the list of fields to display in the dynamic manager.
     * Returns key-value pairs where key is column name and value is type.
     * Example: ['status' => 'string', 'total_amount' => 'money']
     */
    public static function getModuleFields(): array;
}
