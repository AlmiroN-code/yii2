<?php

namespace tests\unit\config;

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
use Yii;

/**
 * Тесты для DI контейнера.
 * Requirements: 3.1, 3.2
 */
class ContainerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before(): void
    {
        // Загружаем конфигурацию контейнера
        $container = require dirname(__DIR__, 3) . '/config/container.php';
        
        foreach ($container['definitions'] as $interface => $implementation) {
            Yii::$container->set($interface, $implementation);
        }
    }

    /**
     * Тест: SlugServiceInterface разрешается в SlugService.
     */
    public function testSlugServiceResolution(): void
    {
        $service = Yii::$container->get(SlugServiceInterface::class);
        
        $this->assertInstanceOf(SlugServiceInterface::class, $service);
        $this->assertInstanceOf(SlugService::class, $service);
    }

    /**
     * Тест: PublicationRepositoryInterface разрешается в PublicationRepository.
     */
    public function testPublicationRepositoryResolution(): void
    {
        $repository = Yii::$container->get(PublicationRepositoryInterface::class);
        
        $this->assertInstanceOf(PublicationRepositoryInterface::class, $repository);
        $this->assertInstanceOf(PublicationRepository::class, $repository);
    }

    /**
     * Тест: UserRepositoryInterface разрешается в UserRepository.
     */
    public function testUserRepositoryResolution(): void
    {
        $repository = Yii::$container->get(UserRepositoryInterface::class);
        
        $this->assertInstanceOf(UserRepositoryInterface::class, $repository);
        $this->assertInstanceOf(UserRepository::class, $repository);
    }

    /**
     * Тест: CategoryRepositoryInterface разрешается в CategoryRepository.
     */
    public function testCategoryRepositoryResolution(): void
    {
        $repository = Yii::$container->get(CategoryRepositoryInterface::class);
        
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $repository);
        $this->assertInstanceOf(CategoryRepository::class, $repository);
    }

    /**
     * Тест: TagRepositoryInterface разрешается в TagRepository.
     */
    public function testTagRepositoryResolution(): void
    {
        $repository = Yii::$container->get(TagRepositoryInterface::class);
        
        $this->assertInstanceOf(TagRepositoryInterface::class, $repository);
        $this->assertInstanceOf(TagRepository::class, $repository);
    }

    /**
     * Тест: PublicationServiceInterface разрешается в PublicationService с зависимостями.
     */
    public function testPublicationServiceResolution(): void
    {
        $service = Yii::$container->get(PublicationServiceInterface::class);
        
        $this->assertInstanceOf(PublicationServiceInterface::class, $service);
        $this->assertInstanceOf(PublicationService::class, $service);
    }

    /**
     * Тест: UserServiceInterface разрешается в UserService с зависимостями.
     */
    public function testUserServiceResolution(): void
    {
        $service = Yii::$container->get(UserServiceInterface::class);
        
        $this->assertInstanceOf(UserServiceInterface::class, $service);
        $this->assertInstanceOf(UserService::class, $service);
    }

    /**
     * Тест: Все интерфейсы из конфигурации разрешаются корректно.
     */
    public function testAllDefinitionsResolve(): void
    {
        $container = require dirname(__DIR__, 3) . '/config/container.php';
        
        foreach ($container['definitions'] as $interface => $implementation) {
            $instance = Yii::$container->get($interface);
            
            $this->assertInstanceOf($interface, $instance, "Interface {$interface} should resolve");
            $this->assertInstanceOf($implementation, $instance, "Interface {$interface} should resolve to {$implementation}");
        }
    }
}
