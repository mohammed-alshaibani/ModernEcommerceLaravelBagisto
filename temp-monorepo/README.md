# Bagisto Monorepo

This is a monorepo setup for Bagisto eCommerce platform, allowing you to manage multiple related packages and projects in a single repository.

## Structure

```
.
├── packages/                   # Shared packages
│   ├── core/                  # Core functionality
│   ├── admin/                 # Admin panel
│   ├── ui/                    # Frontend UI components
│   ├── auth/                  # Authentication module
│   └── api/                   # API module
└── projects/                  # Individual projects
    └── my-bagisto-store/      # Your main Bagisto store
```

## Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js & NPM
- MySQL or MariaDB

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url> bagisto-monorepo
   cd bagisto-monorepo
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install NPM dependencies:
   ```bash
   cd projects/my-bagisto-store
   npm install
   ```

4. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in the `.env` file.

7. Run migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```

8. Link the storage:
   ```bash
   php artisan storage:link
   ```

9. Start the development server:
   ```bash
   php artisan serve
   ```

## Development

### Adding a New Package

1. Create a new directory in the `packages` folder.
2. Add a `composer.json` file with the package configuration.
3. Add the package to the root `composer.json` repositories section.
4. Require the package in your project.

### Working with Packages

- Each package in the `packages` directory is a separate Composer package.
- Use `composer require` to add dependencies to individual packages.
- Use `composer update` in the root directory to update all packages.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
