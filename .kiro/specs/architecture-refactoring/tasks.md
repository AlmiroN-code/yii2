# Implementation Plan

- [x] 1. Создание PHP 8.4 Enums




  - [x] 1.1 Создать enum PublicationStatus


    - Создать файл `enums/PublicationStatus.php`
    - Реализовать cases: DRAFT, PUBLISHED, ARCHIVED
    - Добавить методы label() и labels()
    - _Requirements: 4.1, 4.3_

  - [x] 1.2 Создать enum UserRole

    - Создать файл `enums/UserRole.php`
    - Реализовать cases: USER, AUTHOR, MODERATOR, ADMIN
    - Добавить методы label(), labels(), canCreatePublication(), canModerate()
    - _Requirements: 4.2, 4.3_
  - [x] 1.3 Создать enum UserStatus


    - Создать файл `enums/UserStatus.php`
    - Реализовать cases: INACTIVE, ACTIVE, BANNED
    - Добавить методы label() и labels()
    - _Requirements: 4.1, 4.3_
  - [x] 1.4 Написать property-тест для enum round-trip


    - **Property 3: Enum serialization round-trip**
    - **Validates: Requirements 4.3, 4.4**
    - Установить Eris: `composer require --dev giorgiosironi/eris`
    - Создать `tests/unit/enums/EnumPropertyTest.php`

- [x] 2. Создание Repository Layer





  - [x] 2.1 Создать базовый интерфейс RepositoryInterface


    - Создать файл `repositories/RepositoryInterface.php`
    - Определить методы findById(), findAll(), save(), delete()
    - _Requirements: 2.5_
  - [x] 2.2 Создать PublicationRepository


    - Создать интерфейс `repositories/PublicationRepositoryInterface.php`
    - Создать реализацию `repositories/PublicationRepository.php`
    - Реализовать методы findBySlug(), findPublished(), findByAuthor(), findByCategory(), findByTag()
    - _Requirements: 2.1, 2.5_
  - [x] 2.3 Создать UserRepository


    - Создать интерфейс `repositories/UserRepositoryInterface.php`
    - Создать реализацию `repositories/UserRepository.php`
    - Реализовать методы findByUsername(), findByEmail(), findByUsernameOrEmail(), findActive()
    - _Requirements: 2.2, 2.5_

  - [x] 2.4 Создать CategoryRepository

    - Создать интерфейс `repositories/CategoryRepositoryInterface.php`
    - Создать реализацию `repositories/CategoryRepository.php`
    - Реализовать методы findBySlug(), findRoots(), findChildren()
    - _Requirements: 2.3, 2.5_
  - [x] 2.5 Создать TagRepository


    - Создать интерфейс `repositories/TagRepositoryInterface.php`
    - Создать реализацию `repositories/TagRepository.php`
    - Реализовать методы findBySlug(), findPopular()
    - _Requirements: 2.4, 2.5_
  - [x] 2.6 Написать property-тест для Repository


    - **Property 2: Repository returns correct types**
    - **Validates: Requirements 2.5**
    - Создать `tests/unit/repositories/RepositoryPropertyTest.php`

- [x] 3. Создание Service Layer





  - [x] 3.1 Создать PublicationService

    - Создать интерфейс `services/PublicationServiceInterface.php`
    - Создать реализацию `services/PublicationService.php`
    - Инжектить SlugService и PublicationRepository через конструктор
    - Реализовать методы create(), update(), delete(), publish(), archive(), incrementViews()
    - _Requirements: 1.2, 3.1, 3.2, 3.3, 3.4_

  - [x] 3.2 Создать UserService

    - Создать интерфейс `services/UserServiceInterface.php`
    - Создать реализацию `services/UserService.php`
    - Инжектить UserRepository через конструктор
    - Реализовать методы register(), updateProfile(), changePassword(), changeRole(), ban(), activate()
    - _Requirements: 1.3, 3.1, 3.2, 3.4_
  - [x] 3.3 Написать property-тест для SlugService


    - **Property 1: Slug generation produces valid slugs**
    - **Validates: Requirements 1.1**
    - Создать `tests/unit/services/SlugServicePropertyTest.php`

- [x] 4. Checkpoint





  - Ensure all tests pass, ask the user if questions arise.

- [x] 5. Конфигурация DI Container






  - [x] 5.1 Создать конфигурацию контейнера

    - Создать файл `config/container.php`
    - Зарегистрировать все интерфейсы и их реализации
    - _Requirements: 3.1, 3.2, 3.5_
  - [x] 5.2 Подключить контейнер в web.php


    - Добавить загрузку container.php в config/web.php
    - Настроить автоматическую инъекцию зависимостей
    - _Requirements: 3.1, 3.2_

  - [x] 5.3 Написать тест для DI Container

    - Проверить, что все сервисы и репозитории разрешаются
    - Создать `tests/unit/config/ContainerTest.php`
    - _Requirements: 3.1, 3.2_

- [x] 6. Рефакторинг моделей





  - [x] 6.1 Рефакторинг модели Publication


    - Удалить константы STATUS_*
    - Удалить методы transliterate(), generateSlug()
    - Добавить геттер/сеттер для PublicationStatus enum
    - Обновить beforeSave() для использования SlugService через DI
    - Добавить строгую типизацию
    - _Requirements: 1.1, 1.4, 4.1, 5.1, 5.3, 5.4_

  - [x] 6.2 Рефакторинг модели User

    - Удалить константы STATUS_*, ROLE_*
    - Добавить геттеры/сеттеры для UserStatus и UserRole enums
    - Обновить методы isAdmin(), isAuthor(), canCreatePublication() для работы с enum
    - Добавить строгую типизацию
    - _Requirements: 1.4, 4.2, 5.1, 5.3, 5.4_
  - [x] 6.3 Рефакторинг модели Category


    - Удалить методы transliterate(), generateSlug()
    - Обновить beforeSave() для использования SlugService
    - Добавить строгую типизацию
    - _Requirements: 1.1, 1.4, 5.3, 5.4_
  - [x] 6.4 Рефакторинг модели Tag


    - Удалить методы transliterate(), generateSlug()
    - Обновить beforeSave() для использования SlugService
    - Добавить строгую типизацию
    - _Requirements: 1.1, 1.4, 5.3, 5.4_

- [x] 7. Рефакторинг контроллеров






  - [x] 7.1 Рефакторинг PublicationController

    - Добавить инъекцию PublicationService через конструктор
    - Заменить прямые вызовы ActiveRecord на методы сервиса
    - Обновить работу со статусами на enum
    - _Requirements: 2.1, 3.3, 4.1_

  - [x] 7.2 Рефакторинг ProfileController

    - Добавить инъекцию UserService через конструктор
    - Заменить прямые вызовы ActiveRecord на методы сервиса
    - _Requirements: 2.2, 3.3_

  - [x] 7.3 Рефакторинг SearchService


    - Добавить инъекцию репозиториев через конструктор
    - Заменить прямые вызовы моделей на методы репозиториев
    - Обновить работу со статусами на enum
    - _Requirements: 2.1, 2.3, 2.4, 3.4_

- [x] 8. Рефакторинг админ-модуля






  - [x] 8.1 Рефакторинг admin/PublicationController

    - Добавить инъекцию PublicationService
    - Обновить работу со статусами на enum
    - _Requirements: 2.1, 3.3, 4.1_


  - [x] 8.2 Рефакторинг admin/UserController





    - Добавить инъекцию UserService
    - Обновить работу с ролями и статусами на enum
    - _Requirements: 2.2, 3.3, 4.2_

- [x] 9. Обновление представлений




  - [x] 9.1 Обновить представления публикаций
    - Заменить вызовы констант на методы enum
    - Обновить фильтры по статусу

    - _Requirements: 4.1_
  - [x] 9.2 Обновить представления пользователей


    - Заменить вызовы констант на методы enum
    - Обновить отображение ролей и статусов
    - _Requirements: 4.2_

- [x] 10. Final Checkpoint





  - Ensure all tests pass, ask the user if questions arise.
