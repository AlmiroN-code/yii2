# Requirements Document

## Introduction

Универсальный блог-сайт с публикациями на базе Yii2. Включает систему категорий и тегов, публичный фронтенд и самописную админ-панель. Весь UI построен на Tailwind CSS через npm.

## Glossary

- **Publication**: Статья/публикация с заголовком, контентом, изображением, категорией и тегами
- **Category**: Категория для группировки публикаций (иерархическая структура)
- **Tag**: Метка для дополнительной классификации публикаций (many-to-many)
- **Admin Panel**: Защищённая область для управления контентом
- **Frontend**: Публичная часть сайта для просмотра публикаций

## Requirements

### Requirement 1: Publication Management

**User Story:** As an administrator, I want to manage publications, so that I can create, edit, and publish content on the site.

#### Acceptance Criteria

1. WHEN an admin creates a publication THEN the system SHALL save title, slug, content, excerpt, featured image, status, category, and tags
2. WHEN an admin edits a publication THEN the system SHALL update all fields and modification timestamp
3. WHEN an admin sets publication status to "published" THEN the system SHALL make it visible on frontend
4. WHEN an admin sets publication status to "draft" THEN the system SHALL hide it from frontend
5. IF a publication slug already exists THEN the system SHALL generate a unique slug automatically


### Requirement 2: Category Management

**User Story:** As an administrator, I want to organize publications into categories, so that visitors can browse content by topic.

#### Acceptance Criteria

1. WHEN an admin creates a category THEN the system SHALL save name, slug, description, and optional parent category
2. WHEN an admin assigns a category to a publication THEN the system SHALL link them with a foreign key
3. WHEN a category is deleted THEN the system SHALL set publications' category to null (not cascade delete)
4. WHEN displaying categories THEN the system SHALL support hierarchical tree structure

### Requirement 3: Tag Management

**User Story:** As an administrator, I want to tag publications with keywords, so that visitors can find related content easily.

#### Acceptance Criteria

1. WHEN an admin creates a tag THEN the system SHALL save name and slug
2. WHEN an admin assigns tags to a publication THEN the system SHALL create many-to-many relationships
3. WHEN a tag is deleted THEN the system SHALL remove only the tag-publication links (not publications)
4. WHEN displaying a publication THEN the system SHALL show all associated tags

### Requirement 4: Frontend Display

**User Story:** As a visitor, I want to browse and read publications, so that I can consume the content.

#### Acceptance Criteria

1. WHEN a visitor opens the homepage THEN the system SHALL display paginated list of published publications
2. WHEN a visitor clicks on a publication THEN the system SHALL display full content with category and tags
3. WHEN a visitor clicks on a category THEN the system SHALL display publications filtered by that category
4. WHEN a visitor clicks on a tag THEN the system SHALL display publications filtered by that tag
5. WHEN rendering pages THEN the system SHALL use Tailwind CSS for responsive styling


### Requirement 5: Admin Panel

**User Story:** As an administrator, I want a dedicated admin interface, so that I can manage all content efficiently.

#### Acceptance Criteria

1. WHEN an admin accesses /admin THEN the system SHALL require authentication
2. WHEN authenticated THEN the system SHALL display dashboard with content statistics
3. WHEN managing content THEN the system SHALL provide CRUD interfaces for publications, categories, and tags
4. WHEN rendering admin pages THEN the system SHALL use Tailwind CSS for consistent styling
5. WHEN an admin logs out THEN the system SHALL clear session and redirect to login

### Requirement 6: Tailwind CSS Integration

**User Story:** As a developer, I want Tailwind CSS integrated via npm, so that I can build optimized and maintainable styles.

#### Acceptance Criteria

1. WHEN building assets THEN the system SHALL compile Tailwind CSS via npm scripts
2. WHEN in development THEN the system SHALL support hot-reload for CSS changes
3. WHEN in production THEN the system SHALL purge unused CSS for minimal file size
4. WHEN styling components THEN the system SHALL use Tailwind utility classes

### Requirement 7: SEO and Performance

**User Story:** As a site owner, I want optimized pages, so that search engines can index content effectively.

#### Acceptance Criteria

1. WHEN rendering a publication THEN the system SHALL include meta title, description, and Open Graph tags
2. WHEN generating URLs THEN the system SHALL use SEO-friendly slugs
3. WHEN loading pages THEN the system SHALL implement lazy loading for images
4. WHEN caching is enabled THEN the system SHALL cache rendered pages for performance


## Laragon (окружение разработки)
- Документация: https://laragon.org/docs
- **Веб-сервер**: nginx 1.2.7
- **PHP**: 8.4.15
  - Путь: `D:\laragon\bin\php\php-8.4.15-nts-Win32-vs17-x64\php.exe`
- **NodeJS**: `D:\laragon\bin\nodejs\node-v22`
- **URL доступа**: http://yii2.test:8080/
- **Корневая папка сайта**: /web

Всегда отвечай и пиши на русском языке