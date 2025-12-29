<?php

/**
 * Конфигурация DI контейнера.
 * 
 * Регистрирует все интерфейсы и их реализации для автоматической инъекции зависимостей.
 * Requirements: 3.1, 3.2, 3.5
 */

use app\repositories\CategoryRepository;
use app\repositories\CategoryRepositoryInterface;
use app\repositories\PublicationRepository;
use app\repositories\PublicationRepositoryInterface;
use app\repositories\TagRepository;
use app\repositories\TagRepositoryInterface;
use app\repositories\UserRepository;
use app\repositories\UserRepositoryInterface;
use app\services\PublicationService;
use app\services\PublicationServiceInterface;
use app\services\SlugService;
use app\services\SlugServiceInterface;
use app\services\UserService;
use app\services\UserServiceInterface;

return [
    'definitions' => [
        // Repositories
        PublicationRepositoryInterface::class => PublicationRepository::class,
        UserRepositoryInterface::class => UserRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
        TagRepositoryInterface::class => TagRepository::class,
        
        // Services
        SlugServiceInterface::class => SlugService::class,
        PublicationServiceInterface::class => PublicationService::class,
        UserServiceInterface::class => UserService::class,
    ],
];
