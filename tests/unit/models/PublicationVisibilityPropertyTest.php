<?php

namespace tests\unit\models;

use app\enums\PublicationStatus;
use app\models\Publication;
use app\models\Category;
use app\models\Tag;
use app\models\PublicationTag;
use Eris\Generators;
use Eris\TestTrait;

/**
 * Property-based tests for Publication visibility by status.
 * **Feature: blog-publications, Property 3: Publication visibility by status**
 * **Validates: Requirements 1.3, 1.4**
 */
class PublicationVisibilityPropertyTest extends \Codeception\Test\Unit
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
     * **Feature: blog-publications, Property 3: Publication visibility by status**
     * **Validates: Requirements 1.3, 1.4**
     * 
     * For any publication, it SHALL appear in frontend queries if and only if 
     * its status is "published".
     */
    public function testPublicationVisibilityByStatus()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::choose(1, 5), // Number of draft publications
                Generators::choose(1, 5)  // Number of published publications
            )
            ->then(function ($draftCount, $publishedCount) {
                // Clean up before each iteration
                PublicationTag::deleteAll();
                Publication::deleteAll();
                Tag::deleteAll();
                Category::deleteAll();
                
                $draftIds = [];
                $publishedIds = [];

                // Create draft publications
                for ($i = 0; $i < $draftCount; $i++) {
                    $publication = new Publication();
                    $publication->title = 'Draft Publication ' . uniqid();
                    $publication->content = 'Draft content ' . $i;
                    $publication->setPublicationStatus(PublicationStatus::DRAFT);
                    
                    $this->assertTrue($publication->save(), 'Draft publication should be saved');
                    $draftIds[] = $publication->id;
                }

                // Create published publications
                for ($i = 0; $i < $publishedCount; $i++) {
                    $publication = new Publication();
                    $publication->title = 'Published Publication ' . uniqid();
                    $publication->content = 'Published content ' . $i;
                    $publication->setPublicationStatus(PublicationStatus::PUBLISHED);
                    
                    $this->assertTrue($publication->save(), 'Published publication should be saved');
                    $publishedIds[] = $publication->id;
                }

                // Query for published publications (frontend query)
                $visiblePublications = Publication::findPublished()->all();
                $visibleIds = array_map(function ($p) {
                    return $p->id;
                }, $visiblePublications);

                // Verify only published publications are visible
                $this->assertCount(
                    $publishedCount,
                    $visiblePublications,
                    'Only published publications should be visible'
                );

                // Verify all published publications are in the result
                foreach ($publishedIds as $pubId) {
                    $this->assertContains(
                        $pubId,
                        $visibleIds,
                        'Published publication should be visible'
                    );
                }

                // Verify no draft publications are in the result
                foreach ($draftIds as $draftId) {
                    $this->assertNotContains(
                        $draftId,
                        $visibleIds,
                        'Draft publication should not be visible'
                    );
                }

                // Verify status change affects visibility
                if (!empty($draftIds)) {
                    $draftToPublish = Publication::findOne($draftIds[0]);
                    $draftToPublish->setPublicationStatus(PublicationStatus::PUBLISHED);
                    $draftToPublish->save();

                    $newVisiblePublications = Publication::findPublished()->all();
                    $newVisibleIds = array_map(function ($p) {
                        return $p->id;
                    }, $newVisiblePublications);

                    $this->assertContains(
                        $draftIds[0],
                        $newVisibleIds,
                        'Publication should become visible after status change to published'
                    );
                }

                if (!empty($publishedIds)) {
                    $publishedToDraft = Publication::findOne($publishedIds[0]);
                    $publishedToDraft->setPublicationStatus(PublicationStatus::DRAFT);
                    $publishedToDraft->save();

                    $finalVisiblePublications = Publication::findPublished()->all();
                    $finalVisibleIds = array_map(function ($p) {
                        return $p->id;
                    }, $finalVisiblePublications);

                    $this->assertNotContains(
                        $publishedIds[0],
                        $finalVisibleIds,
                        'Publication should become invisible after status change to draft'
                    );
                }
            });
    }
}
