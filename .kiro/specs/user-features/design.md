# Design Document: User Features

## Обзор

Расширение Yii2 блог-платформы системой авторизации с хранением в БД, профилями пользователей, закладками, оптимизацией изображений, комментариями с модерацией, хлебными крошками, поиском с автодополнением и управлением SEO.

## Архитектура

```mermaid
graph TB
    subgraph Frontend
        A[Views/Layouts] --> B[TailwindCSS]
        A --> C[JavaScript/AJAX]
    end
    
    subgraph Controllers
        D[AuthController]
        E[ProfileController]
        F[FavoriteController]
        G[CommentController]
        H[SearchController]
    end
    
    subgraph Admin Module
        I1[SeoController]
    end
    
    subgraph Models
        I[User - ActiveRecord]
        J[UserProfile]
        K[Favorite]
        L[Comment]
    end
    
    subgraph Services
        M[ImageOptimizer]
        N[SearchService]
        O1[SeoService]
    end
    
    subgraph Components
        O[BreadcrumbsComponent]
        P1[SeoComponent]
    end
    
    Frontend --> Controllers
    Frontend --> Admin Module
    Controllers --> Models
    Controllers --> Services
    Controllers --> Components
    Admin Module --> Services
    Models --> P[(MySQL)]
```

## Компоненты и интерфейсы

### 1. AuthController

```php
namespace app\controllers;

class AuthController extends Controller
{
    public function actionLogin(): string|Response;      // GET/POST /login
    public function actionRegister(): string|Response;  // GET/POST /register
    public function actionLogout(): Response;           // POST /logout
}
```

### 2. ProfileController

```php
namespace app\controllers;

class ProfileController extends Controller
{
    public function actionView(string $username): string;           // GET /profile/{username}
    public function actionEdit(string $username): string|Response;  // GET/POST /profile/{username}/edit
    public function actionPassword(string $username): string|Response; // GET/POST /profile/{username}/password
    public function actionFavorites(string $username): string;      // GET /profile/{username}/favorites
}
```

### 3. FavoriteController (API)

```php
namespace app\controllers;

class FavoriteController extends Controller
{
    public function actionToggle(int $id): array;  // POST /api/favorite/toggle/{id} - AJAX
}
```

### 4. CommentController

```php
namespace app\controllers;

class CommentController extends Controller
{
    public function actionCreate(int $publicationId): array|Response;  // POST - AJAX/Form
}
```

### 5. SearchController (API)

```php
namespace app\controllers;

class SearchController extends Controller
{
    public function actionAutocomplete(string $q): array;  // GET /api/search/autocomplete
    public function actionIndex(string $q): string;        // GET /search
}
```

### 6. SeoController (Admin Module)

```php
namespace app\modules\admin\controllers;

class SeoController extends Controller
{
    // Глобальные настройки SEO
    public function actionIndex(): string|Response;           // GET/POST /admin/seo
    
    // Sitemap
    public function actionSitemap(): string|Response;         // GET/POST /admin/seo/sitemap
    public function actionGenerateSitemap(): Response;        // POST /admin/seo/generate-sitemap
    
    // Robots.txt
    public function actionRobots(): string|Response;          // GET/POST /admin/seo/robots
    
    // Редиректы
    public function actionRedirects(): string;                // GET /admin/seo/redirects
    public function actionCreateRedirect(): string|Response;  // GET/POST /admin/seo/create-redirect
    public function actionUpdateRedirect(int $id): string|Response; // GET/POST /admin/seo/update-redirect/{id}
    public function actionDeleteRedirect(int $id): Response;  // POST /admin/seo/delete-redirect/{id}
    
    // Вебмастер-сервисы
    public function actionWebmaster(): string|Response;       // GET/POST /admin/seo/webmaster
}
```

### 7. ImageOptimizer Service

```php
namespace app\services;

interface ImageOptimizerInterface
{
    public function optimize(string $sourcePath, array $options = []): string;
    public function createThumbnails(string $sourcePath): array;
    public function convertToWebp(string $sourcePath, int $quality = 85): string;
    public function delete(string $path): bool;
}

class ImageOptimizer implements ImageOptimizerInterface
{
    const SIZE_SMALL = [150, 150];
    const SIZE_MEDIUM = [400, 300];
    const SIZE_LARGE = [800, 600];
    const MAX_WIDTH = 1920;
}
```

### 8. SeoService

```php
namespace app\services;

interface SeoServiceInterface
{
    // Глобальные настройки
    public function getGlobalSettings(): array;
    public function saveGlobalSettings(array $data): bool;
    
    // Мета-теги для страницы
    public function getMetaTags(string $type, ?int $id = null): array;
    public function saveMetaTags(string $type, int $id, array $data): bool;
    
    // Sitemap
    public function generateSitemap(): string;
    public function getSitemapSettings(): array;
    public function saveSitemapSettings(array $data): bool;
    
    // Robots.txt
    public function getRobotsContent(): string;
    public function saveRobotsContent(string $content): bool;
    
    // Редиректы
    public function findRedirect(string $sourceUrl): ?Redirect;
    public function createRedirect(string $source, string $target, int $type = 301): Redirect;
    
    // Schema.org
    public function getArticleSchema(Publication $publication): array;
    public function getWebsiteSchema(): array;
    public function getCollectionSchema(Category $category): array;
    
    // SEO-анализ
    public function analyzeContent(string $title, string $description, string $content, ?string $keyword = null): array;
    
    // Canonical URL
    public function getCanonicalUrl(?string $customCanonical = null): string;
}

class SeoService implements SeoServiceInterface
{
    const META_TITLE_MAX_LENGTH = 60;
    const META_DESCRIPTION_MIN_LENGTH = 120;
    const META_DESCRIPTION_MAX_LENGTH = 160;
    const MIN_CONTENT_WORDS = 300;
}
```

### 9. SeoComponent (Application Component)

```php
namespace app\components;

use yii\base\Component;

/**
 * Компонент для рендеринга SEO-тегов в layout
 */
class SeoComponent extends Component
{
    public string $title = '';
    public string $description = '';
    public string $keywords = '';
    public ?string $canonicalUrl = null;
    public array $ogTags = [];
    public array $schemaOrg = [];
    
    // Верификация вебмастеров
    public ?string $googleVerification = null;
    public ?string $yandexVerification = null;
    public ?string $bingVerification = null;
    
    public function init(): void;
    public function setMetaTags(array $tags): void;
    public function registerMetaTags(\yii\web\View $view): void;
    public function registerSchemaOrg(\yii\web\View $view): void;
    public function registerCanonical(\yii\web\View $view): void;
    public function registerWebmasterTags(\yii\web\View $view): void;
}
```

### 10. Breadcrumbs Component

```php
namespace app\components;

class Breadcrumbs extends \yii\base\Component
{
    public static function build(array $items): array;
    public static function forPublication(Publication $publication): array;
    public static function forCategory(Category $category): array;
    public static function forProfile(User $user): array;
}
```

## Модели данных

### User (ActiveRecord) - Замена текущей модели

```php
/**
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string|null $access_token
 * @property int $status (0=inactive, 1=active, 2=banned)
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
```

### UserProfile

```php
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $display_name
 * @property string|null $avatar
 * @property string|null $bio
 * @property string $created_at
 * @property string $updated_at
 */
class UserProfile extends ActiveRecord
```

### Favorite

```php
/**
 * @property int $id
 * @property int $user_id
 * @property int $publication_id
 * @property string $created_at
 */
class Favorite extends ActiveRecord
```

### Comment

```php
/**
 * @property int $id
 * @property int $publication_id
 * @property int|null $user_id
 * @property string|null $guest_name
 * @property string|null $guest_email
 * @property string $content
 * @property int $rating (1-5)
 * @property string $status (pending, approved, rejected, spam)
 * @property string $ip_address
 * @property string $created_at
 * @property string $updated_at
 */
class Comment extends ActiveRecord
```

### SeoSetting

```php
/**
 * Модель для хранения SEO настроек (глобальных и для сущностей)
 * 
 * @property int $id
 * @property string $entity_type (global, publication, category, page)
 * @property int|null $entity_id (null для global)
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $og_title
 * @property string|null $og_description
 * @property string|null $og_image
 * @property string|null $canonical_url
 * @property string|null $robots (index,follow / noindex,nofollow)
 * @property string $created_at
 * @property string $updated_at
 */
class SeoSetting extends ActiveRecord
{
    const TYPE_GLOBAL = 'global';
    const TYPE_PUBLICATION = 'publication';
    const TYPE_CATEGORY = 'category';
    const TYPE_PAGE = 'page';
}
```

### Redirect

```php
/**
 * Модель для хранения 301/302 редиректов
 * 
 * @property int $id
 * @property string $source_url
 * @property string $target_url
 * @property int $type (301, 302)
 * @property int $hits (счётчик переходов)
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class Redirect extends ActiveRecord
{
    const TYPE_PERMANENT = 301;
    const TYPE_TEMPORARY = 302;
}
```

### WebmasterVerification

```php
/**
 * Модель для хранения кодов верификации вебмастер-сервисов
 * 
 * @property int $id
 * @property string $service (google, yandex, bing)
 * @property string $verification_code
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class WebmasterVerification extends ActiveRecord
{
    const SERVICE_GOOGLE = 'google';
    const SERVICE_YANDEX = 'yandex';
    const SERVICE_BING = 'bing';
}
```

### Миграции БД

```sql
-- m250101_100001_create_user_table
CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    auth_key VARCHAR(32) NOT NULL,
    access_token VARCHAR(255),
    status TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- m250101_100002_create_user_profile_table
CREATE TABLE user_profile (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    display_name VARCHAR(100),
    avatar VARCHAR(255),
    bio TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    UNIQUE INDEX idx_user_id (user_id)
);

-- m250101_100003_create_favorite_table
CREATE TABLE favorite (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    publication_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (publication_id) REFERENCES publication(id) ON DELETE CASCADE,
    UNIQUE INDEX idx_user_publication (user_id, publication_id)
);

-- m250101_100004_create_comment_table
CREATE TABLE comment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    publication_id INT NOT NULL,
    user_id INT,
    guest_name VARCHAR(100),
    guest_email VARCHAR(255),
    content TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    status ENUM('pending', 'approved', 'rejected', 'spam') DEFAULT 'pending',
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (publication_id) REFERENCES publication(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE SET NULL,
    INDEX idx_publication_status (publication_id, status),
    INDEX idx_status (status)
);

-- m250101_100005_create_seo_setting_table
CREATE TABLE seo_setting (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entity_type ENUM('global', 'publication', 'category', 'page') NOT NULL,
    entity_id INT DEFAULT NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords VARCHAR(500),
    og_title VARCHAR(255),
    og_description TEXT,
    og_image VARCHAR(500),
    canonical_url VARCHAR(500),
    robots VARCHAR(50) DEFAULT 'index,follow',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_entity (entity_type, entity_id),
    INDEX idx_entity_type (entity_type)
);

-- m250101_100006_create_redirect_table
CREATE TABLE redirect (
    id INT PRIMARY KEY AUTO_INCREMENT,
    source_url VARCHAR(500) NOT NULL,
    target_url VARCHAR(500) NOT NULL,
    type SMALLINT DEFAULT 301,
    hits INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_source_url (source_url),
    INDEX idx_is_active (is_active)
);

-- m250101_100007_create_webmaster_verification_table
CREATE TABLE webmaster_verification (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service ENUM('google', 'yandex', 'bing') NOT NULL,
    verification_code VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_service (service)
);
```

## URL Routing

```php
// config/web.php urlManager rules
'login' => 'auth/login',
'register' => 'auth/register',
'logout' => 'auth/logout',

'profile/<username:[\w-]+>' => 'profile/view',
'profile/<username:[\w-]+>/edit' => 'profile/edit',
'profile/<username:[\w-]+>/password' => 'profile/password',
'profile/<username:[\w-]+>/favorites' => 'profile/favorites',

'api/favorite/toggle/<id:\d+>' => 'favorite/toggle',
'api/search/autocomplete' => 'search/autocomplete',
'search' => 'search/index',

// SEO routes
'sitemap.xml' => 'site/sitemap',
'robots.txt' => 'site/robots',

// Admin SEO routes (в modules/admin/config.php)
'admin/seo' => 'seo/index',
'admin/seo/sitemap' => 'seo/sitemap',
'admin/seo/generate-sitemap' => 'seo/generate-sitemap',
'admin/seo/robots' => 'seo/robots',
'admin/seo/redirects' => 'seo/redirects',
'admin/seo/create-redirect' => 'seo/create-redirect',
'admin/seo/update-redirect/<id:\d+>' => 'seo/update-redirect',
'admin/seo/delete-redirect/<id:\d+>' => 'seo/delete-redirect',
'admin/seo/webmaster' => 'seo/webmaster',
```

## Обработка ошибок

| Ситуация | HTTP код | Действие |
|----------|----------|----------|
| Неверные учётные данные | 200 | Flash message + редирект на форму |
| Профиль не найден | 404 | NotFoundHttpException |
| Нет доступа к редактированию | 403 | ForbiddenHttpException |
| Невалидные данные формы | 200 | Validation errors в форме |
| AJAX ошибка | 400/500 | JSON response с error message |
| Спам-комментарий | 200 | Тихое отклонение (honeypot) |
| Редирект не найден | 404 | NotFoundHttpException |
| Дублирующийся source_url редиректа | 200 | Validation error в форме |
| Ошибка записи robots.txt | 200 | Flash error + лог ошибки |
| Ошибка генерации sitemap | 200 | Flash error + лог ошибки |
| Невалидный canonical URL | 200 | Validation error в форме |

## Стратегия тестирования

### Unit тесты
- User::validatePassword() - проверка хеширования
- ImageOptimizer::convertToWebp() - конвертация изображений
- Comment::isSpam() - детекция спама
- Breadcrumbs::build() - формирование крошек
- SeoService::analyzeContent() - SEO-анализ контента
- SeoService::generateSitemap() - генерация sitemap XML
- SeoService::getArticleSchema() - формирование JSON-LD
- Redirect::findBySourceUrl() - поиск редиректа

### Functional тесты
- Регистрация с валидными/невалидными данными
- Авторизация с remember me
- Редактирование профиля владельцем
- Запрет редактирования чужого профиля
- Toggle закладки через AJAX
- Отправка комментария с honeypot
- Сохранение глобальных SEO настроек
- Создание/редактирование/удаление редиректов
- Генерация sitemap.xml
- Сохранение robots.txt
- Выполнение 301 редиректа
- Сохранение кодов верификации вебмастеров

### Acceptance тесты
- Полный flow регистрации → авторизации → редактирования профиля
- Поиск с автодополнением в браузере
- Проверка мета-тегов на странице публикации
- Проверка JSON-LD разметки в HTML

## Свойства корректности (Correctness Properties)

### SEO Invariants

1. **Уникальность редиректов**: Каждый source_url в таблице redirect должен быть уникальным
   - Проверка: `SELECT source_url, COUNT(*) FROM redirect GROUP BY source_url HAVING COUNT(*) > 1` должен возвращать 0 записей

2. **Отсутствие циклических редиректов**: Цепочка редиректов не должна содержать циклов
   - Проверка: При создании редиректа A→B проверить, что не существует цепочки B→...→A

3. **Валидность canonical URL**: Canonical URL должен быть абсолютным URL того же домена
   - Проверка: URL должен начинаться с базового URL сайта

4. **Целостность SEO настроек**: Для entity_type != 'global' должен существовать entity_id
   - Проверка: `SELECT * FROM seo_setting WHERE entity_type != 'global' AND entity_id IS NULL` должен возвращать 0 записей

5. **Валидность sitemap**: Сгенерированный sitemap должен соответствовать XML Schema sitemap.org
   - Проверка: Валидация XML против XSD схемы

6. **Консистентность мета-тегов**: meta_title не должен превышать 60 символов, meta_description - 160 символов
   - Проверка: Предупреждение при сохранении, но не блокировка

### SEO Postconditions

1. **После изменения slug**: Если slug публикации изменён и пользователь выбрал создание редиректа, должна существовать запись в таблице redirect

2. **После генерации sitemap**: Файл /web/sitemap.xml должен существовать и содержать валидный XML

3. **После сохранения robots.txt**: Файл /web/robots.txt должен существовать и содержать сохранённый контент

4. **После сохранения верификации**: Мета-тег верификации должен присутствовать в HTML всех страниц
