# Tech Stack

## Framework
- Yii 2.0.45+ (PHP MVC framework)
- PHP 8.4.15

## Frontend
- TailwindCSS

## Key Dependencies
- yii2-symfonymailer: Email handling
- yii2-debug: Debug toolbar (dev)
- yii2-gii: Code generator (dev)

## Testing
- Codeception (unit, functional, acceptance tests)

## Development Environment
- Vagrant support available

## Laragon (окружение разработки)
- Документация: https://laragon.org/docs
- **Веб-сервер**: nginx 1.2.7
- **PHP**: 8.4.15
  - Путь: `D:\laragon\bin\php\php-8.4.15-nts-Win32-vs17-x64\php.exe`
- **NodeJS**: `D:\laragon\bin\nodejs\node-v22`
- **URL доступа**: http://yii2.test:8080/
- **Корневая папка сайта**: /web

## Common Commands

```bash
# Install dependencies
composer install

# Update dependencies
composer update --prefer-dist

# Run tests (unit + functional)
vendor/bin/codecept run

# Run specific test suite
vendor/bin/codecept run unit
vendor/bin/codecept run functional

# Run with coverage
vendor/bin/codecept run --coverage --coverage-html

# Console commands
php yii <command>

# Docker
docker-compose up -d                    # Start
docker-compose run --rm php composer install  # Install deps
```

## Database
- Configuration: `config/db.php`
- Test DB: `config/test_db.php`
- Supports MySQL, PostgreSQL, SQLite via PDO
