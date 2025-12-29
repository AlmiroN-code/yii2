# Project Structure

```
├── assets/          # Asset bundles (CSS/JS definitions)
├── commands/        # Console commands (CLI controllers)
├── config/          # Application configuration
│   ├── web.php      # Web app config
│   ├── console.php  # Console app config
│   ├── db.php       # Database connection
│   ├── params.php   # Application parameters
│   └── test.php     # Test environment config
├── controllers/     # Web controllers
├── mail/            # Email view templates
├── models/          # Data models and forms
├── runtime/         # Generated files, logs, cache
├── tests/           # Codeception tests
│   ├── unit/        # Unit tests
│   ├── functional/  # Functional tests
│   └── acceptance/  # Browser acceptance tests
├── vendor/          # Composer dependencies
├── views/           # View templates
│   ├── layouts/     # Layout templates
│   └── site/        # Site controller views
├── web/             # Web root (public)
│   ├── index.php    # Entry point
│   ├── css/         # CSS files
│   └── assets/      # Published assets
└── widgets/         # Reusable UI widgets
```

## Naming Conventions
- Controllers: `PascalCaseController.php` in `app\controllers` namespace
- Models: `PascalCase.php` in `app\models` namespace
- Views: `kebab-case.php` matching action names
- Actions: `actionPascalCase()` methods in controllers

## Entry Points
- Web: `web/index.php`
- Console: `yii` (root)
- Tests: `web/index-test.php`


Всегда отвечай и пиши на русском языке