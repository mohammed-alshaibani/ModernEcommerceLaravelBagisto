# Set error action preference
$ErrorActionPreference = "Stop"

# Function to execute a command and check its result
function Invoke-CommandWithCheck {
    param (
        [string]$Command,
        [string]$ErrorMessage = "Command failed"
    )
    
    Write-Host "`nExecuting: $Command" -ForegroundColor Cyan
    $output = Invoke-Expression $Command 2>&1 | Out-String
    
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Error: $ErrorMessage" -ForegroundColor Red
        Write-Host "Output: $output" -ForegroundColor Red
        exit 1
    }
    
    Write-Host $output -ForegroundColor Green
    return $output
}

# Create the packages directory if it doesn't exist
$packagesDir = Join-Path -Path $PSScriptRoot -ChildPath "packages"
if (-not (Test-Path -Path $packagesDir)) {
    New-Item -ItemType Directory -Path $packagesDir | Out-Null
}

# Get the path to the monorepo packages directory
$monorepoPackagesDir = Resolve-Path -Path "$PSScriptRoot\..\..\packages"

# Create symbolic links for each package type
$packageTypes = @(
    "admin",
    "auth",
    "core",
    "frontend",
    "modules",
    "payment",
    "shipping",
    "theme"
)

Write-Host "Setting up monorepo package links..." -ForegroundColor Yellow

foreach ($type in $packageTypes) {
    $sourceDir = Join-Path -Path $monorepoPackagesDir -ChildPath $type
    $targetDir = Join-Path -Path $packagesDir -ChildPath $type
    
    # Remove the target directory if it exists
    if (Test-Path -Path $targetDir) {
        Remove-Item -Path $targetDir -Recurse -Force -ErrorAction SilentlyContinue
    }
    
    # Create a symbolic link
    try {
        $null = New-Item -ItemType SymbolicLink -Path $targetDir -Target $sourceDir -Force
        Write-Host "Created symbolic link: $targetDir -> $sourceDir" -ForegroundColor Green
    } catch {
        Write-Host "Failed to create symbolic link for $type: $_" -ForegroundColor Red
        exit 1
    }
}

Write-Host "`nSymbolic links created successfully!" -ForegroundColor Green

# Change to the project directory
Push-Location -Path $PSScriptRoot

try {
    # Run composer install if vendor directory doesn't exist
    if (-not (Test-Path -Path "$PSScriptRoot\vendor")) {
        Write-Host "`nRunning composer install..." -ForegroundColor Yellow
        Invoke-CommandWithCheck -Command "composer install --no-interaction --prefer-dist --optimize-autoloader" -ErrorMessage "Composer install failed"
    }

    # Run the package setup script
    Write-Host "`nSetting up packages..." -ForegroundColor Yellow
    $phpPath = (Get-Command php -ErrorAction SilentlyContinue).Source
    
    if (-not $phpPath) {
        $phpPath = "php"
    }
    
    Invoke-CommandWithCheck -Command "$phpPath setup-packages.php" -ErrorMessage "Package setup failed"

    # Publish vendor assets
    Write-Host "`nPublishing vendor assets..." -ForegroundColor Yellow
    Invoke-CommandWithCheck -Command "$phpPath artisan vendor:publish --tag=laravel-assets --ansi --force" -ErrorMessage "Failed to publish vendor assets"

    # Create storage link
    Write-Host "`nCreating storage link..." -ForegroundColor Yellow
    if (-not (Test-Path -Path "$PSScriptRoot\public\storage")) {
        Invoke-CommandWithCheck -Command "$phpPath artisan storage:link" -ErrorMessage "Failed to create storage link"
    } else {
        Write-Host "Storage link already exists." -ForegroundColor Green
    }

    # Clear application cache
    Write-Host "`nClearing application cache..." -ForegroundColor Yellow
    Invoke-CommandWithCheck -Command "$phpPath artisan optimize:clear" -ErrorMessage "Failed to clear application cache"

    # Run database migrations if .env exists
    if (Test-Path -Path "$PSScriptRoot\.env") {
        Write-Host "`nRunning database migrations..." -ForegroundColor Yellow
        Invoke-CommandWithCheck -Command "$phpPath artisan migrate --force" -ErrorMessage "Database migration failed"
    } else {
        Write-Host "`nSkipping database migrations (no .env file found)." -ForegroundColor Yellow
    }

    Write-Host "`nMonorepo setup completed successfully!" -ForegroundColor Green
    Write-Host "You can now run your application using: php artisan serve" -ForegroundColor Cyan
    
} catch {
    Write-Host "`nError during setup: $_" -ForegroundColor Red
    exit 1
} finally {
    # Restore the original directory
    Pop-Location
}
