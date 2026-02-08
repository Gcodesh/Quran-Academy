# Islamic Education Platform - Modern Architecture

## Overview
This platform has been updated to a modern, Clean Architecture approach using PHP 8+, PSR-4 Autoloading, and robust Security practices.

## System Requirements
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer
- PDO Extension

## Directory Structure
- `src/`: Core Application Logic (Namespaced `App\`)
  - `Models/`: Domain Entities (User, Course, etc.)
  - `Services/`: Business Logic (Auth, Media)
  - `Database/`: Database Connection & Helpers
- `migrations/`: Database Migrations
- `tests/`: Automated Tests
- `public/pages/`: Frontend Views (Unchanged visual layer)
- `api/`: API Endpoints

## Installation

1. **Clone & Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Configuration**
   Copy `.env.example` to `.env` and configure your database credentials.
   ```bash
   cp .env.example .env
   ```

3. **Database Setup**
   Run the migration runner to set up the database schema.
   ```bash
   php migrations/run.php
   ```

## Development

### Running Tests
```bash
vendor/bin/phpunit
```

### Adding Migrations
Create a new `.sql` file in `migrations/` directory (e.g., `002_add_table.sql`). Run `php migrations/run.php` to apply.

## Architecture & Security
- **Auth**: Centralized `AuthService` returning standardized JSON responses.
- **Security**: 
  - HttpOnly & Secure Session Cookies
  - Strong Password Hashing
  - MIME-based File Upload Validation in `MediaService`
  - CSRF Protection (Session Token)
- **Database**: Singleton PDO instance with Environment configuration.

## API Compatibility
The system maintains backward compatibility with the existing Frontend and API calls through Proxy classes in `includes/classes` and `includes/functions`.

## CI/CD
A GitHub Actions workflow is provided in `.github/workflows/ci.yml` which runs:
- Composer Install
- Migrations
- Static Analysis (Lint)
- PHPUnit Tests
