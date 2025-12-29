<?php

namespace tests\unit\repositories;

use app\repositories\PublicationRepository;
use app\repositories\UserRepository;
use app\repositories\CategoryRepository;
use app\repositories\TagRepository;
use app\repositories\PublicationRepositoryInterface;
use app\repositories\UserRepositoryInterface;
use app\repositories\CategoryRepositoryInterface;
use app\repositories\TagRepositoryInterface;
use app\models\Publication;
use app\models\User;
use app\models\Category;
use app\models\Tag;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use Eris\Generators;
use Eris\TestTrait;

/**
 * Property-based tests for Repository layer.
 * **Feature: architecture-refactoring, Property 2: Repository returns correct types**
 * **Validates: Requirements 2.5**
 */
class RepositoryPropertyTest extends \Codeception\Test\Unit
{
    use TestTrait;

    /**
     * @var \UnitTester
     */
    protected $tester;

    private PublicationRepository $publicationRepository;
    private UserRepository $userRepository;
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;

    protected function _before(): void
    {
        $this->publicationRepository = new PublicationRepository();
        $this->userRepository = new UserRepository();
        $this->categoryRepository = new CategoryRepository();
        $this->tagRepository = new TagRepository();
    }

    /**
     * **Feature: architecture-refactoring, Property 2: Repository returns correct types**
     * **Validates: Requirements 2.5**
     * 
     * For any repository, findById with a positive integer SHALL return 
     * either null or a model instance of the correct type.
     */
    public function testFindByIdReturnsCorrectType()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::pos()
            )
            ->then(function (int $id) {
                // PublicationRepository
                $publication = $this->publicationRepository->findById($id);
                $this->assertTrue(
                    $publication === null || $publication instanceof Publication,
                    "PublicationRepository::findById should return null or Publication"
                );

                // UserRepository
                $user = $this->userRepository->findById($id);
                $this->assertTrue(
                    $user === null || $user instanceof User,
                    "UserRepository::findById should return null or User"
                );

                // CategoryRepository
                $category = $this->categoryRepository->findById($id);
                $this->assertTrue(
                    $category === null || $category instanceof Category,
                    "CategoryRepository::findById should return null or Category"
                );

                // TagRepository
                $tag = $this->tagRepository->findById($id);
                $this->assertTrue(
                    $tag === null || $tag instanceof Tag,
                    "TagRepository::findById should return null or Tag"
                );
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 2: Repository returns correct types**
     * **Validates: Requirements 2.5**
     * 
     * For any repository, findAll SHALL return an array of model instances.
     */
    public function testFindAllReturnsArrayOfCorrectType()
    {
        // PublicationRepository
        $publications = $this->publicationRepository->findAll();
        $this->assertIsArray($publications, "PublicationRepository::findAll should return array");
        foreach ($publications as $item) {
            $this->assertInstanceOf(
                Publication::class,
                $item,
                "PublicationRepository::findAll should return array of Publication"
            );
        }

        // UserRepository
        $users = $this->userRepository->findAll();
        $this->assertIsArray($users, "UserRepository::findAll should return array");
        foreach ($users as $item) {
            $this->assertInstanceOf(
                User::class,
                $item,
                "UserRepository::findAll should return array of User"
            );
        }

        // CategoryRepository
        $categories = $this->categoryRepository->findAll();
        $this->assertIsArray($categories, "CategoryRepository::findAll should return array");
        foreach ($categories as $item) {
            $this->assertInstanceOf(
                Category::class,
                $item,
                "CategoryRepository::findAll should return array of Category"
            );
        }

        // TagRepository
        $tags = $this->tagRepository->findAll();
        $this->assertIsArray($tags, "TagRepository::findAll should return array");
        foreach ($tags as $item) {
            $this->assertInstanceOf(
                Tag::class,
                $item,
                "TagRepository::findAll should return array of Tag"
            );
        }
    }

    /**
     * **Feature: architecture-refactoring, Property 2: Repository returns correct types**
     * **Validates: Requirements 2.5**
     * 
     * For any slug string, findBySlug SHALL return either null or a model instance.
     */
    public function testFindBySlugReturnsCorrectType()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::string()
            )
            ->then(function (string $slug) {
                // PublicationRepository
                $publication = $this->publicationRepository->findBySlug($slug);
                $this->assertTrue(
                    $publication === null || $publication instanceof Publication,
                    "PublicationRepository::findBySlug should return null or Publication"
                );

                // CategoryRepository
                $category = $this->categoryRepository->findBySlug($slug);
                $this->assertTrue(
                    $category === null || $category instanceof Category,
                    "CategoryRepository::findBySlug should return null or Category"
                );

                // TagRepository
                $tag = $this->tagRepository->findBySlug($slug);
                $this->assertTrue(
                    $tag === null || $tag instanceof Tag,
                    "TagRepository::findBySlug should return null or Tag"
                );
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 2: Repository returns correct types**
     * **Validates: Requirements 2.5**
     * 
     * For PublicationRepository, query methods SHALL return ActiveQuery instances.
     */
    public function testPublicationQueryMethodsReturnActiveQuery()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::pos()
            )
            ->then(function (int $id) {
                $this->assertInstanceOf(
                    ActiveQuery::class,
                    $this->publicationRepository->findPublished(),
                    "PublicationRepository::findPublished should return ActiveQuery"
                );

                $this->assertInstanceOf(
                    ActiveQuery::class,
                    $this->publicationRepository->findByAuthor($id),
                    "PublicationRepository::findByAuthor should return ActiveQuery"
                );

                $this->assertInstanceOf(
                    ActiveQuery::class,
                    $this->publicationRepository->findByCategory($id),
                    "PublicationRepository::findByCategory should return ActiveQuery"
                );

                $this->assertInstanceOf(
                    ActiveQuery::class,
                    $this->publicationRepository->findByTag($id),
                    "PublicationRepository::findByTag should return ActiveQuery"
                );
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 2: Repository returns correct types**
     * **Validates: Requirements 2.5**
     * 
     * For UserRepository, findByUsername and findByEmail SHALL return null or User.
     */
    public function testUserRepositoryFindMethodsReturnCorrectType()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::string()
            )
            ->then(function (string $identity) {
                $byUsername = $this->userRepository->findByUsername($identity);
                $this->assertTrue(
                    $byUsername === null || $byUsername instanceof User,
                    "UserRepository::findByUsername should return null or User"
                );

                $byEmail = $this->userRepository->findByEmail($identity);
                $this->assertTrue(
                    $byEmail === null || $byEmail instanceof User,
                    "UserRepository::findByEmail should return null or User"
                );

                $byUsernameOrEmail = $this->userRepository->findByUsernameOrEmail($identity);
                $this->assertTrue(
                    $byUsernameOrEmail === null || $byUsernameOrEmail instanceof User,
                    "UserRepository::findByUsernameOrEmail should return null or User"
                );
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 2: Repository returns correct types**
     * **Validates: Requirements 2.5**
     * 
     * For UserRepository, findActive SHALL return ActiveQuery.
     */
    public function testUserRepositoryFindActiveReturnsActiveQuery()
    {
        $this->assertInstanceOf(
            ActiveQuery::class,
            $this->userRepository->findActive(),
            "UserRepository::findActive should return ActiveQuery"
        );
    }

    /**
     * **Feature: architecture-refactoring, Property 2: Repository returns correct types**
     * **Validates: Requirements 2.5**
     * 
     * For CategoryRepository, findRoots and findChildren SHALL return arrays of Category.
     */
    public function testCategoryRepositoryHierarchyMethodsReturnCorrectType()
    {
        $roots = $this->categoryRepository->findRoots();
        $this->assertIsArray($roots, "CategoryRepository::findRoots should return array");
        foreach ($roots as $item) {
            $this->assertInstanceOf(
                Category::class,
                $item,
                "CategoryRepository::findRoots should return array of Category"
            );
        }

        $this
            ->limitTo(100)
            ->forAll(
                Generators::pos()
            )
            ->then(function (int $parentId) {
                $children = $this->categoryRepository->findChildren($parentId);
                $this->assertIsArray(
                    $children,
                    "CategoryRepository::findChildren should return array"
                );
                foreach ($children as $item) {
                    $this->assertInstanceOf(
                        Category::class,
                        $item,
                        "CategoryRepository::findChildren should return array of Category"
                    );
                }
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 2: Repository returns correct types**
     * **Validates: Requirements 2.5**
     * 
     * For TagRepository, findPopular SHALL return array of Tag.
     */
    public function testTagRepositoryFindPopularReturnsCorrectType()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::choose(1, 100)
            )
            ->then(function (int $limit) {
                $popular = $this->tagRepository->findPopular($limit);
                $this->assertIsArray(
                    $popular,
                    "TagRepository::findPopular should return array"
                );
                foreach ($popular as $item) {
                    $this->assertInstanceOf(
                        Tag::class,
                        $item,
                        "TagRepository::findPopular should return array of Tag"
                    );
                }
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 2: Repository returns correct types**
     * **Validates: Requirements 2.5**
     * 
     * Verify that all repositories implement their interfaces.
     */
    public function testRepositoriesImplementInterfaces()
    {
        $this->assertInstanceOf(
            PublicationRepositoryInterface::class,
            $this->publicationRepository,
            "PublicationRepository should implement PublicationRepositoryInterface"
        );

        $this->assertInstanceOf(
            UserRepositoryInterface::class,
            $this->userRepository,
            "UserRepository should implement UserRepositoryInterface"
        );

        $this->assertInstanceOf(
            CategoryRepositoryInterface::class,
            $this->categoryRepository,
            "CategoryRepository should implement CategoryRepositoryInterface"
        );

        $this->assertInstanceOf(
            TagRepositoryInterface::class,
            $this->tagRepository,
            "TagRepository should implement TagRepositoryInterface"
        );
    }
}
