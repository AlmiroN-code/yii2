# Implementation Plan

## 1. Система ролей

- [x] 1.1 Создать миграцию для добавления роли в user
  - Добавить поле `role` VARCHAR(20) DEFAULT 'user'
  - Создать индекс на role
  - Установить role='admin' для первого пользователя
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 1.2 Обновить модель User
  - Добавить константы ROLE_USER, ROLE_AUTHOR, ROLE_ADMIN
  - Добавить методы isAdmin(), isAuthor(), canCreatePublication()
  - Добавить метод canEditPublication(Publication $publication)
  - Добавить getRoleLabels() и getRoleLabel()
  - Обновить rules() для валидации role
  - _Requirements: 1.4, 1.5, 1.6, 1.7_

## 2. Управление пользователями в админке

- [x] 2.1 Создать UserController в админ-модуле
  - actionIndex() — список пользователей с пагинацией и фильтрами
  - actionUpdate($id) — редактирование пользователя
  - actionDelete($id) — удаление пользователя
  - AccessControl только для админов
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_

- [x] 2.2 Создать модель UserEditForm
  - Поля: username, email, role, status
  - Валидация уникальности username и email
  - Метод save() для обновления пользователя
  - _Requirements: 2.3_

- [x] 2.3 Создать views для управления пользователями
  - modules/admin/views/user/index.php — список с фильтрами
  - modules/admin/views/user/update.php — форма редактирования
  - _Requirements: 2.1, 2.2, 2.3_

## 3. Создание и редактирование публикаций

- [x] 3.1 Создать модель PublicationForm
  - Поля: title, slug, content, excerpt, category_id, tagIds, featured_image, status
  - Валидация обязательных полей
  - Метод save() для создания/обновления публикации
  - Интеграция с ImageOptimizer для обложки
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

- [x] 3.2 Расширить PublicationController
  - actionCreate() — создание публикации (только author/admin)
  - actionUpdate($id) — редактирование публикации
  - actionDelete($id) — удаление публикации
  - actionMy() — список публикаций автора
  - AccessControl для проверки прав
  - _Requirements: 3.1-3.6, 4.1-4.6, 5.1-5.5_

- [x] 3.3 Создать views для публикаций автора
  - views/publication/create.php — форма создания
  - views/publication/update.php — форма редактирования
  - views/publication/my.php — список публикаций автора
  - _Requirements: 3.1, 4.1, 5.1, 5.2, 5.3_

## 4. Навигация и интеграция

- [x] 4.1 Обновить навигацию в layout
  - Добавить "Мои публикации" для авторов/админов
  - Добавить кнопку "Написать статью" для авторов/админов
  - Добавить кнопку "Редактировать" на странице публикации для автора
  - _Requirements: 7.1, 7.2, 7.3_

- [x] 4.2 Обновить меню админ-панели
  - Добавить ссылку на "Пользователи" (/admin/user)
  - Добавить ссылку на "Комментарии" (/admin/comment)
  - Добавить ссылку на "Админ-панель" в меню пользователя для админов
  - _Requirements: 2.1, 6.1, 7.4_

## 5. URL маршруты

- [x] 5.1 Обновить config/web.php
  - Добавить маршруты: publication/create, publication/update, publication/delete, publication/my
  - Добавить маршруты: admin/user, admin/user/update, admin/user/delete
  - _Requirements: 3.1, 4.1, 2.1_
