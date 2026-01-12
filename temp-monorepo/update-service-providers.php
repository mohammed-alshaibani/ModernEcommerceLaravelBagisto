<?php

// Path to the project root
$rootPath = dirname(__DIR__);

// Function to update service provider files
function updateServiceProvider($file) {
    $content = file_get_contents($file);
    
    // Update namespace paths
    $content = preg_replace(
        '/namespace\s+Webkul\\\\([^\\\\]+)\\\\Providers/',
        'namespace Webkul\\$1\\Providers',
        $content
    );
    
    // Update class references
    $content = preg_replace(
        '/use\s+Webkul\\\\([^\\\\]+)\\\\([^;]+);/',
        'use Webkul\\\\$1\\\\$2;',
        $content
    );
    
    // Update extends
    $content = preg_replace(
        '/extends\s+\\\\?Webkul\\\\([^\\\\]+)\\\\Providers\\\\([^\s{]+)/',
        'extends \\\\Webkul\\\\$1\\\\Providers\\\\$2',
        $content
    );
    
    file_put_contents($file, $content);
}

// Find all service provider files
$serviceProviders = [];
$directories = [
    $rootPath . '/packages/admin',
    $rootPath . '/packages/auth',
    $rootPath . '/packages/core',
    $rootPath . '/packages/frontend',
    $rootPath . '/packages/modules',
    $rootPath . '/packages/payment',
    $rootPath . '/packages/shipping',
    $rootPath . '/packages/theme'
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        continue;
    }
    
    $providerFiles = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($providerFiles as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), 'ServiceProvider.php')) {
            updateServiceProvider($file->getPathname());
        }
    }
}

echo "Service providers updated successfully.\n";
