<?php

namespace tests\unit\models;

use app\models\Tag;
use app\models\Publication;
use app\models\PublicationTag;
use Eris\Generators;
use Eris\TestTrait;

/**
 * Property-based tests for Tag model.
 * **Feature: blog-publications, Property 5: Tag many-to-many integrity**
 * **Validates: Requirements 3.2, 3.3**
 */
class TagPropertyTest extends \Codeception\Test\Unit
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
    }

    protected function _after()
    {
        // Clean up test data after each test
        PublicationTag::deleteAll();
        Publication::deleteAll();
        Tag::deleteAll();
    }

    /**
     * **Feature: blog-publications, Property 5: Tag many-to-many integrity**
     * **Validates: Requirements 3.2, 3.3**
     * 
     * For any publication with assigned tags, the junction table SHALL contain 
     * exactly one record per tag-publication pair, and deleting a tag SHALL 
     * remove only junction records (not publications).
     */
    public function testTagManyToManyIntegrity()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::choose(1, 3), // Number of tags to create
                Generators::choose(1, 3)  // Number of publications to create
            )
            ->then(function ($tagCount, $publicationCount) {
                // Clean up before each iteration
                PublicationTag::deleteAll();
                Publication::deleteAll();
                Tag::deleteAll();
                
                // Create tags
                $tagIds = [];
                for ($i = 0; $i < $tagCount; $i++) {
                    $tag = new Tag();
                    $tag->name = 'Tag ' . uniqid();
                    $this->assertTrue($tag->save(), 'Tag should be saved');
                    $tagIds[] = $tag->id;
                }

                // Create publications with tags
                $publicationIds = [];
                for ($i = 0; $i < $publicationCount; $i++) {
                    $publication = new Publication();
                    $publication->title = 'Publication ' . uniqid();
                    $publication->content = 'Content for publication ' . $i;
                    $publication->tagIds = $tagIds;
                    $this->assertTrue($publication->save(), 'Publication should be saved');
                    $publicationIds[] = $publication->id;
                }

                // Verify junction table has exactly one record per tag-publication pair
                $expectedJunctionCount = $tagCount * $publicationCount;
                $actualJunctionCount = PublicationTag::find()->count();
                $this->assertEquals(
                    $expectedJunctionCount,
                    $actualJunctionCount,
                    "Junction table should have exactly {$expectedJunctionCount} records"
                );

                // Verify each publication has all tags
                foreach ($publicationIds as $pubId) {
                    $publication = Publication::findOne($pubId);
                    $this->assertCount(
                        $tagCount,
                        $publication->tags,
                        'Each publication should have all tags'
                    );
                }

                // Delete one tag and verify publications are preserved
                $tagToDelete = Tag::findOne($tagIds[0]);
                $tagToDelete->delete();

                // Verify tag is deleted
                $this->assertNull(
                    Tag::findOne($tagIds[0]),
                    'Tag should be deleted'
                );

                // Verify all publications still exist
                foreach ($publicationIds as $pubId) {
                    $publication = Publication::findOne($pubId);
                    $this->assertNotNull(
                        $publication,
                        'Publication should still exist after tag deletion'
                    );
                }

                // Verify junction records for deleted tag are removed
                $junctionRecordsForDeletedTag = PublicationTag::find()
                    ->where(['tag_id' => $tagIds[0]])
                    ->count();
                $this->assertEquals(
                    0,
                    $junctionRecordsForDeletedTag,
                    'Junction records for deleted tag should be removed'
                );

                // Verify remaining junction records
                $remainingJunctionCount = PublicationTag::find()->count();
                $expectedRemainingCount = ($tagCount - 1) * $publicationCount;
                $this->assertEquals(
                    $expectedRemainingCount,
                    $remainingJunctionCount,
                    'Remaining junction records should match expected count'
                );
            });
    }
}
