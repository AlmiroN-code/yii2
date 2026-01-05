# Implementation Plan: SEO Management

## Обзор

Реализация системы управления SEO в админке Yii2 блог-платформы: глобальные мета-теги, SEO для публикаций/категорий, sitemap, robots.txt, редиректы, Schema.org, canonical URL, интеграция с вебмастер-сервисами и SEO-анализ контента.

## Tasks

- [x] 1. Создание миграций и моделей для SEO
  - [x] 1.1 Создать миграцию `m250101_100005_create_seo_setting_table`
    - Таблица seo_setting с полями: entity_type, entity_id, meta_title, meta_description, meta_keywords, og_*, canonical_url, robots
    - Уникальный индекс по (entity_type, entity_id)
    - _Requirements: 8.1, 8.2, 8.8_
  - [x] 1.2 Создать миграцию `m250101_100006_create_redirect_table`
    - Таблица redirect с полями: source_url, target_url, type, hits, is_active
    - Уникальный индекс по source_url
    - _Requirements: 8.3, 8.6_
  - [x] 1.3 Создать миграцию `m250101_100007_create_webmaster_verification_table`
    - Таблица webmaster_verification с полями: service, verification_code, is_active
    - Уникальный индекс по service
    - _Requirements: 8.9_
  - [x] 1.4 Создать модель `SeoSetting` с валидацией и связями
    - Константы TYPE_GLOBAL, TYPE_PUBLICATION, TYPE_CATEGORY, TYPE_PAGE
    - Методы для получения настроек по типу и ID
    - _Requirements: 8.1, 8.2_
  - [x] 1.5 Создать модель `Redirect` с валидацией уникальности source_url
    - Константы TYPE_PERMANENT (301), TYPE_TEMPORARY (302)
    - Метод incrementHits() для счётчика переходов
    - _Requirements: 8.6_
  - [x] 1.6 Создать модель `WebmasterVerification` с валидацией
    - Константы SERVICE_GOOGLE, SERVICE_YANDEX, SERVICE_BING
    - _Requirements: 8.9_

- [x] 2. Checkpoint - Проверка миграций
  - Запустить миграции и убедиться, что таблицы созданы корректно

- [x] 3. Создание SeoService
  - [x] 3.1 Создать интерфейс `SeoServiceInterface`
    - Методы для глобальных настроек, мета-тегов, sitemap, robots.txt, редиректов, Schema.org, SEO-анализа
    - _Requirements: 8.1-8.10_
  - [x] 3.2 Реализовать `SeoService::getGlobalSettings()` и `saveGlobalSettings()`
    - Получение/сохранение глобальных SEO настроек из seo_setting где entity_type='global'
    - _Requirements: 8.1_
  - [x] 3.3 Реализовать `SeoService::getMetaTags()` и `saveMetaTags()`
    - Получение мета-тегов для публикации/категории с fallback на глобальные
    - Автогенерация meta_title из заголовка, meta_description из контента
    - _Requirements: 8.2_
  - [x] 3.4 Реализовать `SeoService::generateSitemap()`
    - Генерация XML sitemap со всеми публикациями, категориями, статическими страницами
    - Включение loc, lastmod, changefreq, priority
    - Сохранение в /web/sitemap.xml
    - _Requirements: 8.4_
  - [x] 3.5 Реализовать `SeoService::getRobotsContent()` и `saveRobotsContent()`
    - Чтение/запись /web/robots.txt
    - Создание базового robots.txt если не существует
    - _Requirements: 8.5_
  - [x] 3.6 Реализовать `SeoService::findRedirect()` и `createRedirect()`
    - Поиск редиректа по source_url
    - Создание редиректа с проверкой на циклы
    - _Requirements: 8.6_
  - [x] 3.7 Реализовать методы Schema.org: `getArticleSchema()`, `getWebsiteSchema()`, `getCollectionSchema()`
    - JSON-LD разметка для Article, WebSite, CollectionPage
    - _Requirements: 8.7_
  - [x] 3.8 Реализовать `SeoService::analyzeContent()`
    - Анализ заголовка (макс 60 символов), описания (120-160), контента (мин 300 слов)
    - Проверка наличия ключевого слова в заголовке
    - Проверка наличия изображений
    - Возврат оценки (хорошо/средне/плохо) с рекомендациями
    - _Requirements: 8.10_
  - [x] 3.9 Реализовать `SeoService::getCanonicalUrl()`
    - Возврат кастомного canonical или текущего URL
    - _Requirements: 8.8_
  - [x] 3.10 Зарегистрировать SeoService в DI контейнере
    - Добавить в config/container.php
    - _Requirements: 8.1-8.10_

- [x] 4. Checkpoint - Проверка SeoService
  - Убедиться, что сервис корректно инжектится и методы работают

- [x] 5. Создание SeoComponent
  - [x] 5.1 Создать `SeoComponent` в components/
    - Свойства: title, description, keywords, canonicalUrl, ogTags, schemaOrg
    - Свойства верификации: googleVerification, yandexVerification, bingVerification
    - _Requirements: 8.1, 8.7, 8.8, 8.9_
  - [x] 5.2 Реализовать методы регистрации мета-тегов
    - `registerMetaTags()` - регистрация title, description, keywords, OG tags
    - `registerSchemaOrg()` - вставка JSON-LD в head
    - `registerCanonical()` - регистрация canonical link
    - `registerWebmasterTags()` - регистрация тегов верификации
    - _Requirements: 8.1, 8.7, 8.8, 8.9_
  - [x] 5.3 Зарегистрировать SeoComponent в конфигурации приложения
    - Добавить в config/web.php как компонент 'seo'
    - _Requirements: 8.1_

- [x] 6. Создание SeoController в админке
  - [x] 6.1 Создать `SeoController` в modules/admin/controllers/
    - Базовая структура с behaviors для доступа только админам
    - _Requirements: 8.1_
  - [x] 6.2 Реализовать `actionIndex()` - глобальные SEO настройки
    - Форма: site_title, site_description, site_keywords, default_og_image
    - _Requirements: 8.1_
  - [x] 6.3 Реализовать `actionSitemap()` и `actionGenerateSitemap()`
    - Настройки sitemap, кнопка генерации, исключение страниц
    - _Requirements: 8.4_
  - [x] 6.4 Реализовать `actionRobots()`
    - Текстовый редактор для robots.txt с предупреждением о синтаксисе
    - _Requirements: 8.5_
  - [x] 6.5 Реализовать CRUD для редиректов
    - `actionRedirects()` - список редиректов с пагинацией
    - `actionCreateRedirect()` - создание редиректа
    - `actionUpdateRedirect()` - редактирование редиректа
    - `actionDeleteRedirect()` - удаление редиректа
    - _Requirements: 8.6_
  - [x] 6.6 Реализовать `actionWebmaster()`
    - Форма для кодов верификации Google, Yandex, Bing
    - _Requirements: 8.9_

- [x] 7. Создание views для SeoController
  - [x] 7.1 Создать view `index.php` - форма глобальных настроек
    - _Requirements: 8.1_
  - [x] 7.2 Создать view `sitemap.php` - настройки и генерация sitemap
    - _Requirements: 8.4_
  - [x] 7.3 Создать view `robots.php` - редактор robots.txt
    - _Requirements: 8.5_
  - [x] 7.4 Создать views для редиректов: `redirects.php`, `create-redirect.php`, `update-redirect.php`
    - _Requirements: 8.6_
  - [x] 7.5 Создать view `webmaster.php` - форма верификации
    - _Requirements: 8.9_

- [x] 8. Checkpoint - Проверка админки SEO
  - Проверить все страницы админки SEO, сохранение настроек

- [x] 9. Интеграция SEO в публичную часть
  - [x] 9.1 Добавить обработку редиректов в bootstrap приложения
    - Проверка URL на наличие редиректа при каждом запросе
    - Выполнение 301/302 редиректа с инкрементом счётчика
    - _Requirements: 8.6_
  - [x] 9.2 Интегрировать SeoComponent в layout
    - Вызов registerMetaTags(), registerSchemaOrg(), registerCanonical(), registerWebmasterTags()
    - _Requirements: 8.1, 8.7, 8.8, 8.9_
  - [x] 9.3 Добавить роуты для sitemap.xml и robots.txt в SiteController
    - `actionSitemap()` - возврат XML sitemap
    - `actionRobots()` - возврат содержимого robots.txt
    - _Requirements: 8.4, 8.5_
  - [x] 9.4 Интегрировать SEO в контроллер публикаций
    - Установка мета-тегов, canonical, Schema.org Article
    - _Requirements: 8.2, 8.7, 8.8_
  - [x] 9.5 Интегрировать SEO в контроллер категорий
    - Установка мета-тегов, Schema.org CollectionPage
    - _Requirements: 8.2, 8.7_
  - [x] 9.6 Добавить Schema.org WebSite на главную страницу
    - _Requirements: 8.7_

- [x] 10. Интеграция SEO в формы редактирования
  - [x] 10.1 Добавить SEO-секцию в форму редактирования публикации
    - Поля: meta_title, meta_description, og_title, og_description, og_image, canonical_url
    - Предложение создать редирект при изменении slug
    - _Requirements: 8.2, 8.3, 8.8_
  - [x] 10.2 Добавить SEO-секцию в форму редактирования категории
    - Поля: meta_title, meta_description
    - _Requirements: 8.2_
  - [x] 10.3 Добавить панель SEO-анализа в форму публикации
    - Отображение рекомендаций в реальном времени (JavaScript)
    - Цветовая индикация оценки
    - _Requirements: 8.10_

- [x] 11. Добавление URL routes
  - [x] 11.1 Добавить публичные роуты в config/web.php
    - `sitemap.xml` => `site/sitemap`
    - `robots.txt` => `site/robots`
    - _Requirements: 8.4, 8.5_
  - [x] 11.2 Добавить админские роуты для SEO
    - Все роуты /admin/seo/*
    - _Requirements: 8.1-8.10_

- [x] 12. Checkpoint - Финальная проверка
  - Проверить все SEO функции: мета-теги, sitemap, robots, редиректы, Schema.org, верификация

## Notes

- Все задачи связаны с Требованием 8 (Управление SEO в админке)
- SeoService использует паттерн Repository для работы с моделями
- SeoComponent регистрируется как application component для доступа через `Yii::$app->seo`
- Редиректы проверяются на циклы при создании
- SEO-анализ работает на клиенте (JavaScript) для мгновенной обратной связи
