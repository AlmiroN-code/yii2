# Implementation Plan

## 1. Система авторизации

- [x] 1.1 Создать миграцию для таблицы user
  - Поля: id, username, email, password_hash, auth_key, access_token, status, created_at, updated_at
  - Индексы на username, email, status
  - _Requirements: 1.1-1.9_

- [x] 1.2 Создать модель User (ActiveRecord)
  - Реализовать IdentityInterface
  - Методы: setPassword(), validatePassword(), generateAuthKey()
  - Правила валидации для username, email, password
  - _Requirements: 1.6, 1.7_

- [x] 1.3 Создать модель RegisterForm
  - Поля: username, email, password, password_confirm
  - Валидация уникальности username и email
  - Метод register() для создания пользователя
  - _Requirements: 1.5, 1.6, 1.7_

- [x] 1.4 Обновить модель LoginForm
  - Адаптировать под новую модель User (ActiveRecord)
  - Поддержка входа по username или email
  - _Requirements: 1.1-1.4_

- [x] 1.5 Создать AuthController
  - actionLogin() - форма входа и обработка
  - actionRegister() - форма регистрации и обработка
  - actionLogout() - выход из системы
  - _Requirements: 1.1-1.9_

- [x] 1.6 Создать views для авторизации
  - views/auth/login.php - форма входа с TailwindCSS
  - views/auth/register.php - форма регистрации с TailwindCSS
  - _Requirements: 1.1, 1.5_

- [x] 1.7 Обновить config/web.php
  - URL rules для /login, /register, /logout
  - Обновить identityClass на новую модель User
  - _Requirements: 1.1, 1.5, 1.8_

## 2. Профили пользователей

- [x] 2.1 Создать миграцию для таблицы user_profile
  - Поля: id, user_id, display_name, avatar, bio, created_at, updated_at
  - Foreign key на user(id) с CASCADE delete
  - _Requirements: 2.1-2.7_

- [x] 2.2 Создать модель UserProfile
  - Связь belongsTo с User
  - Правила валидации для avatar, bio
  - _Requirements: 2.1, 2.2, 2.3_

- [x] 2.3 Обновить модель User
  - Добавить связь hasOne с UserProfile
  - Метод getDisplayName() - возвращает display_name или username
  - _Requirements: 2.1_

- [x] 2.4 Создать модели форм профиля
  - ProfileEditForm - редактирование профиля
  - PasswordChangeForm - смена пароля
  - _Requirements: 2.2, 2.5, 2.6_

- [x] 2.5 Создать ProfileController
  - actionView($username) - публичный профиль
  - actionEdit($username) - редактирование (только владелец)
  - actionPassword($username) - смена пароля (только владелец)
  - Проверка доступа через AccessControl
  - _Requirements: 2.1-2.7_

- [x] 2.6 Создать views для профиля
  - views/profile/view.php - публичный профиль
  - views/profile/edit.php - форма редактирования
  - views/profile/password.php - форма смены пароля
  - _Requirements: 2.1, 2.2, 2.5_

- [x] 2.7 Добавить URL rules для профиля
  - /profile/{username}, /profile/{username}/edit, /profile/{username}/password
  - _Requirements: 2.1, 2.2, 2.5_

## 3. ImageOptimizer

- [x] 3.1 Создать сервис ImageOptimizer
  - Интерфейс ImageOptimizerInterface
  - Метод optimize() - основная оптимизация
  - Метод convertToWebp() - конвертация в WebP
  - Метод createThumbnails() - создание превью (small, medium, large)
  - Метод delete() - удаление с thumbnails
  - Fallback если GD/Imagick недоступны
  - _Requirements: 4.1-4.5_

- [x] 3.2 Интегрировать ImageOptimizer в загрузку аватаров
  - Обработка в ProfileController::actionEdit()
  - Сохранение в web/uploads/avatars/
  - _Requirements: 2.3, 4.1, 4.2_

## 4. Закладки (Избранное)

- [x] 4.1 Создать миграцию для таблицы favorite
  - Поля: id, user_id, publication_id, created_at
  - Уникальный индекс на (user_id, publication_id)
  - Foreign keys с CASCADE delete
  - _Requirements: 3.1-3.5_

- [x] 4.2 Создать модель Favorite
  - Связи belongsTo с User и Publication
  - Статический метод toggle($userId, $publicationId)
  - Статический метод isFavorite($userId, $publicationId)
  - _Requirements: 3.1, 3.2_

- [x] 4.3 Обновить модель Publication
  - Добавить связь hasMany с Favorite
  - Метод getFavoritesCount()
  - _Requirements: 3.5_

- [x] 4.4 Создать FavoriteController (API)
  - actionToggle($id) - AJAX toggle закладки
  - Возврат JSON с новым состоянием
  - _Requirements: 3.1, 3.2_

- [x] 4.5 Добавить actionFavorites в ProfileController
  - Список избранных публикаций с пагинацией
  - views/profile/favorites.php
  - _Requirements: 3.3_

- [x] 4.6 Создать виджет FavoriteButton
  - widgets/FavoriteButton.php
  - JavaScript для AJAX toggle
  - Стили TailwindCSS
  - _Requirements: 3.1, 3.2, 3.4_

## 5. Комментарии

- [x] 5.1 Создать миграцию для таблицы comment
  - Поля: id, publication_id, user_id, guest_name, guest_email, content, rating, status, ip_address, created_at, updated_at
  - Foreign keys с CASCADE/SET NULL
  - Индексы на (publication_id, status)
  - _Requirements: 5.1-5.7_

- [x] 5.2 Создать модель Comment
  - Связи belongsTo с Publication и User
  - Константы статусов: PENDING, APPROVED, REJECTED, SPAM
  - Метод isSpam() - проверка на запрещённые слова
  - Scope для approved комментариев
  - _Requirements: 5.1, 5.4, 5.5, 5.7_

- [x] 5.3 Создать модель CommentForm
  - Поля: name, email, content, rating, honeypot
  - Валидация honeypot (должен быть пустым)
  - Метод save() с автозаполнением для авторизованных
  - _Requirements: 5.1, 5.2, 5.3_

- [x] 5.4 Создать CommentController
  - actionCreate($publicationId) - создание комментария
  - Поддержка AJAX и обычной формы
  - _Requirements: 5.1-5.4_

- [x] 5.5 Создать виджет CommentForm
  - widgets/CommentForm.php
  - Форма с рейтингом звёздами
  - Honeypot поле (скрытое CSS)
  - _Requirements: 5.1, 5.3_

- [x] 5.6 Создать виджет CommentList
  - widgets/CommentList.php
  - Отображение approved комментариев
  - Средний рейтинг публикации
  - _Requirements: 5.5_

- [x] 5.7 Добавить админ-контроллер для модерации
  - modules/admin/controllers/CommentController.php
  - Список комментариев с фильтрами по статусу
  - Действия: approve, reject, delete
  - _Requirements: 5.5, 5.6_

## 6. Хлебные крошки

- [x] 6.1 Создать компонент Breadcrumbs
  - components/Breadcrumbs.php
  - Статические методы: forPublication(), forCategory(), forProfile(), forTag()
  - Интеграция с Yii2 breadcrumbs widget
  - _Requirements: 6.1-6.5_

- [x] 6.2 Добавить методы buildBreadcrumbs в контроллеры
  - PublicationController - категория + публикация
  - CategoryController - иерархия категорий
  - ProfileController - профили
  - TagController - теги
  - _Requirements: 6.2, 6.3, 6.4_

- [x] 6.3 Обновить layout для отображения хлебных крошек
  - views/layouts/main.php - добавить Breadcrumbs widget
  - Стили TailwindCSS
  - _Requirements: 6.1, 6.5_

## 7. Поиск с автодополнением

- [x] 7.1 Создать SearchService
  - services/SearchService.php
  - Метод autocomplete($query, $limit) - поиск по публикациям, категориям, тегам
  - Метод search($query, $page) - полный поиск с пагинацией
  - _Requirements: 7.2, 7.6_

- [x] 7.2 Создать SearchController
  - actionAutocomplete() - API для автодополнения
  - actionIndex() - страница результатов поиска
  - _Requirements: 7.1-7.6_

- [x] 7.3 Создать views для поиска
  - views/search/index.php - страница результатов
  - _Requirements: 7.5_

- [x] 7.4 Создать JavaScript для автодополнения
  - web/js/search-autocomplete.js
  - Debounce 300ms
  - Выпадающий список результатов
  - Навигация клавиатурой
  - _Requirements: 7.1, 7.3, 7.4_

- [x] 7.5 Обновить header layout
  - Добавить поле поиска
  - Подключить search-autocomplete.js
  - _Requirements: 7.1_

## 8. Финальная интеграция

- [x] 8.1 Обновить навигацию в layout
  - Ссылки на профиль для авторизованных
  - Кнопки Login/Register для гостей
  - _Requirements: 1.1, 2.1_

- [x] 8.2 Интегрировать комментарии в страницу публикации
  - views/publication/view.php - добавить CommentForm и CommentList
  - _Requirements: 5.1_

- [x] 8.3 Интегрировать кнопку закладки в карточки публикаций
  - views/publication/view.php
  - views/publication/_card.php (если есть)
  - _Requirements: 3.1_

## 9. Тестирование

- [ ] 9.1 Unit тесты для моделей
  - tests/unit/models/UserTest.php - validatePassword, setPassword, generateAuthKey
  - tests/unit/models/CommentTest.php - isSpam, статусы
  - tests/unit/models/FavoriteTest.php - toggle, isFavorite
  - _Requirements: 1.6, 5.7, 3.1, 3.2_

- [ ] 9.2 Unit тесты для сервисов
  - tests/unit/services/ImageOptimizerTest.php - convertToWebp, createThumbnails
  - tests/unit/services/SearchServiceTest.php - autocomplete, search
  - _Requirements: 4.1, 4.2, 7.2_

- [ ] 9.3 Functional тесты для авторизации
  - tests/functional/AuthCest.php - login, register, logout
  - Проверка валидации, remember me, редиректов
  - _Requirements: 1.1-1.9_

- [ ] 9.4 Functional тесты для профиля
  - tests/functional/ProfileCest.php - view, edit, password
  - Проверка доступа владельца vs не-владельца
  - _Requirements: 2.1-2.7_

- [ ] 9.5 Functional тесты для закладок и комментариев
  - tests/functional/FavoriteCest.php - AJAX toggle
  - tests/functional/CommentCest.php - создание, honeypot
  - _Requirements: 3.1-3.5, 5.1-5.7_
