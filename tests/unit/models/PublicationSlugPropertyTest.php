<?php

namespace tests\unit\models;

use app\models\Publication;
use app\models\Category;
use app\models\Tag;
use app\models\PublicationTag;
use Eris\Generators;
use Eris\TestTrait;

/**
 * Property-based tests for Publication slug uniqueness.
 * **Feature: blog-publications, Property 2: Slug uniqueness**
 * **Validates: Requirements 1.5**
 */
class PublicationSlugPropertyTest extends \Codeception\Test\Unit
{
    use TestTrait;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        // Clean up test data before each test
        PublicationTag::deleteAll();
        Publication::deleteAll();
        Tag::deleteAll();
        Category::deleteAll();
    }

    protected function _after()
    {
        // Clean up test data after each test
        PublicationTag::deleteAll();
        Publication::deleteAll();
        Tag::deleteAll();
        Category::deleteAll();
    }

    /**
     * **Feature: blog-publications, Property 2: Slug uniqueness**
     * **Validates: Requirements 1.5**
     * 
     * For any set of publications with potentially duplicate titles, 
     * the system SHALL generate unique slugs for each publication.
     */
    public function testSlugUniqueness()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::choose(2, 5), // Number of publications with same title
                Generators::elements([
                    'Test Article',
                    'My Blog Post',
                    'News Update',
                    'Тестовая статья', // Cyrillic title
                    'Hello World'
                ])
            )
            ->then(function ($count, $title) {
                // Clean up before each iteration
                PublicationTag::deleteAll();
                Publication::deleteAll();
                Tag::deleteAll();
                Category::deleteAll();
                
                $slugs = [];
                $publicationIds = [];

                // Create multiple publications with the same title
                for ($i = 0; $i < $count; $i++) {
                    $publication = new Publication();
                    $publication->title = $title;
                    $publication->content = 'Content for publication ' . $i;
                    
                    $this->assertTrue(
                        $publication->save(),
                        'Publication should be saved: ' . json_encode($publication->errors)
                    );
                    
                    $publicationIds[] = $publication->id;
                    $slugs[] = $publication->slug;
                }

                // Verify all slugs are unique
                $uniqueSlugs = array_unique($slugs);
                $this->assertCount(
                    $count,
                    $uniqueSlugs,
                    'All slugs should be unique. Got: ' . implode(', ', $slugs)
                );

                // Verify slugs are not empty
                foreach ($slugs as $slug) {
                    $this->assertNotEmpty($slug, 'Slug should not be empty');
                    $this->assertMatchesRegularExpression(
                        '/^[a-z0-9-]+$/',
                        $slug,
                        'Slug should only contain lowercase letters, numbers, and hyphens'
                    );
                }

                // Verify database constraint is satisfied
                $dbSlugs = Publication::find()
                    ->select('slug')
                    ->where(['id' => $publicationIds])
                    ->column();
                
                $this->assertCount(
                    $count,
                    array_unique($dbSlugs),
                    'Database should have unique slugs'
                );
            });
    }
}
