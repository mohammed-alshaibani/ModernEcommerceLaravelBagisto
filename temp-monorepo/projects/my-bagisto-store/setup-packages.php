<?php

/**
 * This script sets up the package structure and ensures all package service providers are properly registered.
 * Run this script after cloning the repository or adding new packages.
 */

$rootDir = __DIR__;
$packagesDir = dirname($rootDir) . '/packages';

// Ensure the packages directory exists
if (!is_dir($packagesDir)) {
    die("Error: Packages directory not found at {$packagesDir}\n");
}

// Get all package service providers
$packageServiceProviders = [];

// Function to find all service providers in a directory
function findServiceProviders($dir, &$providers, $baseNamespace) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getFilename() === 'ModuleServiceProvider.php') {
            $relativePath = $iterator->getSubPathName();
            $namespace = str_replace('/', '\\', $baseNamespace . '\\' . dirname(str_replace('\\', '/', $relativePath)));
            $className = $file->getBasename('.php');
            $fqcn = $namespace . '\\' . $className;
            $providers[] = $fqcn;
        }
    }
}

// Scan packages directory for service providers
$packageDirs = new DirectoryIterator($packagesDir);
foreach ($packageDirs as $packageDir) {
    if ($packageDir->isDir() && !$packageDir->isDot()) {
        $packageName = $packageDir->getFilename();
        $srcDir = $packageDir->getPathname() . '/src';
        
        if (is_dir($srcDir)) {
            $baseNamespace = "Webkul\\{$packageName}";
            findServiceProviders($srcDir, $packageServiceProviders, $baseNamespace);
        } else {
            // Handle nested package structure (e.g., Payment/Paypal)
            $subDirs = new DirectoryIterator($packageDir->getPathname());
            foreach ($subDirs as $subDir) {
                if ($subDir->isDir() && !$subDir->isDot()) {
                    $subPackageName = $subDir->getFilename();
                    $srcDir = $subDir->getPathname() . '/src';
                    
                    if (is_dir($srcDir)) {
                        $baseNamespace = "Webkul\\{$packageName}\\{$subPackageName}";
                        findServiceProviders($srcDir, $packageServiceProviders, $baseNamespace);
                    }
                }
            }
        }
    }
}

// Update the concord.php config file
$configFile = $rootDir . '/config/concord.php';
if (!file_exists($configFile)) {
    die("Error: config/concord.php not found.\n");
}

$config = require $configFile;
$existingProviders = $config['modules'] ?? [];
$existingProviderClasses = array_map('strval', $existingProviders);

// Add any missing providers
$added = 0;
foreach ($packageServiceProviders as $provider) {
    $providerClass = '\\' . ltrim($provider, '\\');
    if (!in_array($providerClass, $existingProviderClasses)) {
        $existingProviders[] = $providerClass;
        $added++;
        echo "Added provider: {$providerClass}\n";
    }
}

// Update the config file if needed
if ($added > 0) {
    $config['modules'] = $existingProviders;
    $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
    file_put_contents($configFile, $configContent);
    echo "\nUpdated config/concord.php with {$added} new service providers.\n";
} else {
    echo "No new service providers found. All packages are already registered.\n";
}

echo "\nPackage setup completed successfully!\n";

echo "\nNext steps:\n";
echo "1. Run 'composer dump-autoload' to update the autoloader\n";
echo "2. Run 'php artisan vendor:publish --tag=laravel-assets --ansi --force' to publish assets\n";
echo "3. Run 'php artisan storage:link' to create the storage link\n";

// Run composer dump-autoload
echo "\nRunning 'composer dump-autoload'...\n";
exec('composer dump-autoload', $output, $returnVar);

echo implode("\n", $output) . "\n";

if ($returnVar !== 0) {
    echo "\nWarning: 'composer dump-autoload' failed with exit code {$returnVar}. Please run it manually.\n";
} else {
    echo "\nAutoloader updated successfully!\n";
}

echo "\nSetup complete! You can now run your application.\n";
