<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Services;

use Illuminate\Support\Facades\File;
use MyPlatform\EcommerceCore\Contracts\ModuleInterface;
use ReflectionClass;
use SplFileInfo;

class ModuleManager
{
    protected string $modulesPath;

    public function __construct()
    {
        $this->modulesPath = __DIR__ . '/../Modules';
    }

    /**
     * Scan and return list of active modules implementing ModuleInterface.
     */
    public function getRegisteredModules(): array
    {
        $modules = [];
        $directories = File::directories($this->modulesPath);

        foreach ($directories as $directory) {
            $modelsPath = $directory . '/Models';
            if (!File::isDirectory($modelsPath)) {
                continue;
            }

            $moduleName = basename($directory);
            $files = File::files($modelsPath);

            foreach ($files as $file) {
                $className = $file->getBasename('.php');
                $fullClass = "MyPlatform\\EcommerceCore\\Modules\\{$moduleName}\\Models\\{$className}";

                if (class_exists($fullClass)) {
                    $reflector = new ReflectionClass($fullClass);

                    if ($reflector->implementsInterface(ModuleInterface::class)) {
                        $modules[] = [
                            'name' => $fullClass::getModuleName(),
                            'class' => $fullClass,
                            'fields' => $fullClass::getModuleFields(),
                        ];
                    }
                }
            }
        }

        return $modules;
    }
}
