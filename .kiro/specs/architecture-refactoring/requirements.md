# Requirements Document

## Introduction

Данная спецификация описывает архитектурный рефакторинг Yii2 блог-платформы. Цель — улучшить организацию кода, вынести бизнес-логику из моделей в сервисы, внедрить Repository pattern и использовать DI-контейнер Yii для управления зависимостями. Это создаст основу для дальнейших улучшений безопасности, производительности и тестируемости.

## Glossary

- **Service Layer**: Слой сервисов, содержащий бизнес-логику приложения
- **Repository Pattern**: Паттерн, абстрагирующий доступ к данным от бизнес-логики
- **DI Container**: Dependency Injection контейнер для управления зависимостями
- **ActiveRecord**: ORM-паттерн Yii2 для работы с базой данных

## Requirements

### Requirement 1

**User Story:** As a developer, I want business logic extracted from models into dedicated services, so that models remain focused on data representation and the codebase is easier to maintain and test.

#### Acceptance Criteria

1. WHEN the Publication model needs slug generation THEN the SlugService SHALL handle all transliteration and slug creation logic
2. WHEN a new publication is created THEN the PublicationService SHALL coordinate validation, slug generation, and persistence
3. WHEN user-related business operations are performed THEN the UserService SHALL handle profile updates, avatar processing, and role management
4. WHEN the transliterate() method is called THEN the SlugService SHALL be the single source of transliteration logic (removing duplication from Publication model)

### Requirement 2

**User Story:** As a developer, I want to use the Repository pattern for data access, so that controllers are decoupled from ActiveRecord and the code is more testable.

#### Acceptance Criteria

1. WHEN a controller needs to fetch publications THEN the PublicationRepository SHALL provide methods for querying publications
2. WHEN a controller needs to fetch users THEN the UserRepository SHALL provide methods for querying users
3. WHEN a controller needs to fetch categories THEN the CategoryRepository SHALL provide methods for querying categories
4. WHEN a controller needs to fetch tags THEN the TagRepository SHALL provide methods for querying tags
5. WHEN a repository method is called THEN the repository SHALL return model instances or collections, abstracting query building from controllers

### Requirement 3

**User Story:** As a developer, I want services and repositories registered in Yii's DI container, so that dependencies are injected automatically and the code follows SOLID principles.

#### Acceptance Criteria

1. WHEN the application bootstraps THEN the DI container SHALL have all services registered with their interfaces
2. WHEN the application bootstraps THEN the DI container SHALL have all repositories registered with their interfaces
3. WHEN a controller is instantiated THEN the required services SHALL be injected via constructor
4. WHEN a service is instantiated THEN the required repositories SHALL be injected via constructor
5. WHEN configuration changes are needed THEN the container configuration SHALL be centralized in a single config file

### Requirement 4

**User Story:** As a developer, I want to use PHP 8.4 Enums instead of class constants, so that status and role values are type-safe and self-documenting.

#### Acceptance Criteria

1. WHEN publication status is referenced THEN the PublicationStatus enum SHALL provide DRAFT, PUBLISHED, and ARCHIVED values
2. WHEN user role is referenced THEN the UserRole enum SHALL provide USER, AUTHOR, MODERATOR, and ADMIN values
3. WHEN status or role is stored in database THEN the enum SHALL serialize to its string or integer value
4. WHEN status or role is retrieved from database THEN the enum SHALL be hydrated from the stored value

### Requirement 5

**User Story:** As a developer, I want strict return type declarations on all methods, so that the code is more predictable and IDE support is improved.

#### Acceptance Criteria

1. WHEN a service method is defined THEN the method SHALL have explicit parameter types and return type declarations
2. WHEN a repository method is defined THEN the method SHALL have explicit parameter types and return type declarations
3. WHEN a model method is defined THEN the method SHALL have explicit parameter types and return type declarations where applicable
4. IF a method can return null THEN the return type SHALL use nullable type syntax (?Type or Type|null)
