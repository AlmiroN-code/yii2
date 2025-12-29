# Design Document

## Overview

Блог-система на Yii2 с публикациями, категориями и тегами. Архитектура следует MVC паттерну Yii2 с разделением на публичный фронтенд и защищённую админ-панель. Стилизация через Tailwind CSS, собираемый npm.

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Web Layer                            │
├─────────────────────────────┬───────────────────────────────┤
│      Frontend (public)      │      Admin Panel (protected)  │
│   - PublicationController   │   - admin/PublicationController│
│   - CategoryController      │   - admin/CategoryController   │
│   - TagController           │   - admin/TagController        │
│   - SiteController          │   - admin/DashboardController  │
└─────────────────────────────┴───────────────────────────────┘
                              │
┌─────────────────────────────┴───────────────────────────────┐
│                      Service Layer                          │
│   - PublicationService (business logic)                     │
│   - SlugService (URL generation)                            │
│   - ImageService (upload handling)                          │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────┴───────────────────────────────┐
│                       Data Layer                            │
│   Models: Publication, Category, Tag, PublicationTag, User  │
│   ActiveRecord with relations                               │
└─────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### Controllers

**Frontend Controllers** (`controllers/`)
- `PublicationController` - список и просмотр публикаций
- `CategoryController` - фильтрация по категориям  
- `TagController` - фильтрация по тегам

**Admin Controllers** (`modules/admin/controllers/`)
- `DefaultController` - dashboard со статистикой
- `PublicationController` - CRUD публикаций
- `CategoryController` - CRUD категорий
- `TagController` - CRUD тегов
- `AuthController` - login/logout

### Services (`services/`)

```php
interface SlugServiceInterface {
    public function generate(string $title, string $table, ?int $excludeId = null): string;
}

interface ImageServiceInterface {
    public function upload(UploadedFile $file, string $folder): string;
    public function delete(string $path): bool;
}
```

### Widgets (`widgets/`)
- `CategoryTreeWidget` - иерархическое дерево категорий
- `TagCloudWidget` - облако тегов
- `PaginationWidget` - кастомная пагинация с Tailwind

## Data Models

### Database Schema

```sql
-- Categories (hierarchical)
CREATE TABLE category (
    id INT PRIMARY KEY AUTO_INCREMENT,
    parent_id INT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES category(id) ON DELETE SET NULL
);

-- Tags
CREATE TABLE tag (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Publications
CREATE TABLE publication (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    status ENUM('draft', 'published') DEFAULT 'draft',
    meta_title VARCHAR(255),
    meta_description TEXT,
    views INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE SET NULL
);

-- Publication-Tag junction
CREATE TABLE publication_tag (
    publication_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (publication_id, tag_id),
    FOREIGN KEY (publication_id) REFERENCES publication(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE CASCADE
);
```

### ActiveRecord Models

```php
// models/Publication.php
class Publication extends ActiveRecord {
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    
    public function getCategory(): ActiveQuery;
    public function getTags(): ActiveQuery;  // via publication_tag
    public function getTagIds(): array;
    public function setTagIds(array $ids): void;
}

// models/Category.php  
class Category extends ActiveRecord {
    public function getParent(): ActiveQuery;
    public function getChildren(): ActiveQuery;
    public function getPublications(): ActiveQuery;
}

// models/Tag.php
class Tag extends ActiveRecord {
    public function getPublications(): ActiveQuery;  // via publication_tag
}
```

## Tailwind CSS Setup

### File Structure
```
├── package.json
├── tailwind.config.js
├── postcss.config.js
└── src/
    └── css/
        ├── app.css          # Frontend styles
        └── admin.css        # Admin styles
```

### Build Configuration

```javascript
// tailwind.config.js
module.exports = {
  content: [
    './views/**/*.php',
    './modules/admin/views/**/*.php',
    './widgets/**/*.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

### NPM Scripts
```json
{
  "scripts": {
    "dev": "npx tailwindcss -i ./src/css/app.css -o ./web/css/app.css --watch",
    "dev:admin": "npx tailwindcss -i ./src/css/admin.css -o ./web/css/admin.css --watch",
    "build": "npx tailwindcss -i ./src/css/app.css -o ./web/css/app.css --minify",
    "build:admin": "npx tailwindcss -i ./src/css/admin.css -o ./web/css/admin.css --minify"
  }
}
```

## URL Structure

### Frontend Routes
- `/` - homepage with latest publications
- `/publication/{slug}` - single publication
- `/category/{slug}` - publications by category
- `/tag/{slug}` - publications by tag

### Admin Routes
- `/admin` - dashboard
- `/admin/publication` - publications list
- `/admin/publication/create` - create publication
- `/admin/publication/update/{id}` - edit publication
- `/admin/category`, `/admin/tag` - same pattern
- `/admin/auth/login`, `/admin/auth/logout`

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Publication data integrity
*For any* valid publication data (title, content, category, tags), creating a publication SHALL result in a persisted record with all fields correctly stored and retrievable.
**Validates: Requirements 1.1**

### Property 2: Slug uniqueness
*For any* set of publications with potentially duplicate titles, the system SHALL generate unique slugs for each publication.
**Validates: Requirements 1.5**

### Property 3: Publication visibility by status
*For any* publication, it SHALL appear in frontend queries if and only if its status is "published".
**Validates: Requirements 1.3, 1.4**

### Property 4: Category deletion preserves publications
*For any* category with associated publications, deleting the category SHALL set those publications' category_id to null without deleting the publications.
**Validates: Requirements 2.3**

### Property 5: Tag many-to-many integrity
*For any* publication with assigned tags, the junction table SHALL contain exactly one record per tag-publication pair, and deleting a tag SHALL remove only junction records.
**Validates: Requirements 3.2, 3.3**

## Error Handling

- **Validation errors**: Return to form with field-specific error messages
- **404 errors**: Custom error page for missing publications/categories/tags
- **403 errors**: Redirect to login for unauthorized admin access
- **Image upload errors**: Display error message, keep form data
- **Database errors**: Log error, show generic message to user

## Testing Strategy

### Unit Tests (Codeception)
- Model validation rules
- Slug generation service
- Category tree building
- Tag assignment/removal

### Property-Based Tests
Using `eris/eris` PHP library for property-based testing:
- Slug uniqueness across random titles
- Publication visibility filtering
- Category deletion cascade behavior
- Tag junction table integrity

Each property test should run minimum 100 iterations.

### Functional Tests
- Publication CRUD workflow
- Category CRUD workflow
- Tag CRUD workflow
- Admin authentication flow
- Frontend pagination and filtering

### Test Annotations
Property-based tests must include comment:
```php
// **Feature: blog-publications, Property {N}: {property_text}**
```

## Admin Panel Layout

```
┌──────────────────────────────────────────────────────────┐
│  Logo    Dashboard  Publications  Categories  Tags  [Logout] │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ┌─────────────────────────────────────────────────────┐ │
│  │                    Content Area                      │ │
│  │                                                      │ │
│  │  - Data tables with search/filter                   │ │
│  │  - Forms with Tailwind styling                      │ │
│  │  - Flash messages for actions                       │ │
│  │                                                      │ │
│  └─────────────────────────────────────────────────────┘ │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

## Frontend Layout

```
┌──────────────────────────────────────────────────────────┐
│  Logo                              Categories  Tags      │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ┌────────────────────────────┐  ┌────────────────────┐ │
│  │     Main Content           │  │    Sidebar         │ │
│  │                            │  │                    │ │
│  │  - Publication cards       │  │  - Categories      │ │
│  │  - Pagination              │  │  - Popular tags    │ │
│  │                            │  │  - Recent posts    │ │
│  └────────────────────────────┘  └────────────────────┘ │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                        Footer                            │
└──────────────────────────────────────────────────────────┘
```
