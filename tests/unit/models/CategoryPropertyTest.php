<?php

namespace tests\unit\models;

use app\models\Category;
use app\models\Publication;
use Eris\Generators;
use Eris\TestTrait;

/**
 * Property-based tests for Category model.
 * **Feature: blog-publications, Property 4: Category deletion preserves publications**
 * **Validates: Requirements 2.3**
 */
class CategoryPropertyTest extends \Codeception\Test\Unit
{
    use TestTrait;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        // Clean up test data before each test
        Publication::deleteAll();
        Category::deleteAll();
    }

    protected function _after()
    {
        // Clean up test data after each test
        Publication::deleteAll();
        Category::deleteAll();
    }

    /**
     * **Feature: blog-publications, Property 4: Category deletion preserves publications**
     * **Validates: Requirements 2.3**
     * 
     * For any category with associated publications, deleting the category 
     * SHALL set those publications' category_id to null without deleting the publications.
     */
    public function testCategoryDeletionPreservesPublications()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::choose(1, 5), // Number of publications to create
                Generators::elements(['Test Category', 'News', 'Articles', 'Blog', 'Updates'])
            )
            ->then(function ($publicationCount, $categoryName) {
                // Clean up before each iteration
                Publication::deleteAll();
                Category::deleteAll();
                
                // Create a category
                $category = new Category();
                $category->name = $categoryName . ' ' . uniqid();
                $this->assertTrue($category->save(), 'Category should be saved');
                $categoryId = $category->id;

                // Create publications in this category
                $publicationIds = [];
                for ($i = 0; $i < $publicationCount; $i++) {
                    $publication = new Publication();
                    $publication->title = 'Test Publication ' . uniqid();
                    $publication->content = 'Test content for publication ' . $i;
                    $publication->category_id = $categoryId;
                    $this->assertTrue($publication->save(), 'Publication should be saved');
                    $publicationIds[] = $publication->id;
                }

                // Verify publications are linked to category
                $this->assertEquals(
                    $publicationCount,
                    Publication::find()->where(['category_id' => $categoryId])->count(),
                    'All publications should be linked to category'
                );

                // Delete the category
                $category->delete();

                // Verify category is deleted
                $this->assertNull(
                    Category::findOne($categoryId),
                    'Category should be deleted'
                );

                // Verify all publications still exist
                foreach ($publicationIds as $pubId) {
                    $publication = Publication::findOne($pubId);
                    $this->assertNotNull(
                        $publication,
                        'Publication should still exist after category deletion'
                    );
                    $this->assertNull(
                        $publication->category_id,
                        'Publication category_id should be null after category deletion'
                    );
                }

                // Verify total publication count is preserved
                $this->assertEquals(
                    $publicationCount,
                    count($publicationIds),
                    'Number of publications should be preserved'
                );
            });
    }
}
