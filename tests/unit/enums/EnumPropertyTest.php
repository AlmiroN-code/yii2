<?php

namespace tests\unit\enums;

use app\enums\PublicationStatus;
use app\enums\UserRole;
use app\enums\UserStatus;
use Eris\Generators;
use Eris\TestTrait;

/**
 * Property-based tests for Enum serialization round-trip.
 * **Feature: architecture-refactoring, Property 3: Enum serialization round-trip**
 * **Validates: Requirements 4.3, 4.4**
 */
class EnumPropertyTest extends \Codeception\Test\Unit
{
    use TestTrait;

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * **Feature: architecture-refactoring, Property 3: Enum serialization round-trip**
     * **Validates: Requirements 4.3, 4.4**
     * 
     * For any PublicationStatus enum value, serializing to database format 
     * and deserializing back SHALL produce the original enum value.
     */
    public function testPublicationStatusRoundTrip()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::elements(PublicationStatus::cases())
            )
            ->then(function (PublicationStatus $status) {
                // Serialize to database value (string)
                $serialized = $status->value;
                
                // Deserialize back to enum
                $deserialized = PublicationStatus::from($serialized);
                
                // Verify round-trip produces original value
                $this->assertSame(
                    $status,
                    $deserialized,
                    "PublicationStatus round-trip failed for {$status->value}"
                );
                
                // Verify label is consistent
                $this->assertSame(
                    $status->label(),
                    $deserialized->label(),
                    "PublicationStatus label should be consistent after round-trip"
                );
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 3: Enum serialization round-trip**
     * **Validates: Requirements 4.3, 4.4**
     * 
     * For any UserRole enum value, serializing to database format 
     * and deserializing back SHALL produce the original enum value.
     */
    public function testUserRoleRoundTrip()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::elements(UserRole::cases())
            )
            ->then(function (UserRole $role) {
                // Serialize to database value (string)
                $serialized = $role->value;
                
                // Deserialize back to enum
                $deserialized = UserRole::from($serialized);
                
                // Verify round-trip produces original value
                $this->assertSame(
                    $role,
                    $deserialized,
                    "UserRole round-trip failed for {$role->value}"
                );
                
                // Verify methods are consistent after round-trip
                $this->assertSame(
                    $role->label(),
                    $deserialized->label(),
                    "UserRole label should be consistent after round-trip"
                );
                $this->assertSame(
                    $role->canCreatePublication(),
                    $deserialized->canCreatePublication(),
                    "UserRole canCreatePublication should be consistent after round-trip"
                );
                $this->assertSame(
                    $role->canModerate(),
                    $deserialized->canModerate(),
                    "UserRole canModerate should be consistent after round-trip"
                );
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 3: Enum serialization round-trip**
     * **Validates: Requirements 4.3, 4.4**
     * 
     * For any UserStatus enum value, serializing to database format 
     * and deserializing back SHALL produce the original enum value.
     */
    public function testUserStatusRoundTrip()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::elements(UserStatus::cases())
            )
            ->then(function (UserStatus $status) {
                // Serialize to database value (int)
                $serialized = $status->value;
                
                // Deserialize back to enum
                $deserialized = UserStatus::from($serialized);
                
                // Verify round-trip produces original value
                $this->assertSame(
                    $status,
                    $deserialized,
                    "UserStatus round-trip failed for {$status->value}"
                );
                
                // Verify label is consistent
                $this->assertSame(
                    $status->label(),
                    $deserialized->label(),
                    "UserStatus label should be consistent after round-trip"
                );
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 3: Enum serialization round-trip**
     * **Validates: Requirements 4.3, 4.4**
     * 
     * Verify that labels() method returns all cases for all enums.
     */
    public function testLabelsContainAllCases()
    {
        // PublicationStatus
        $publicationLabels = PublicationStatus::labels();
        $this->assertCount(
            count(PublicationStatus::cases()),
            $publicationLabels,
            'PublicationStatus::labels() should contain all cases'
        );
        foreach (PublicationStatus::cases() as $case) {
            $this->assertArrayHasKey(
                $case->value,
                $publicationLabels,
                "PublicationStatus::labels() should contain key for {$case->value}"
            );
        }

        // UserRole
        $roleLabels = UserRole::labels();
        $this->assertCount(
            count(UserRole::cases()),
            $roleLabels,
            'UserRole::labels() should contain all cases'
        );
        foreach (UserRole::cases() as $case) {
            $this->assertArrayHasKey(
                $case->value,
                $roleLabels,
                "UserRole::labels() should contain key for {$case->value}"
            );
        }

        // UserStatus
        $statusLabels = UserStatus::labels();
        $this->assertCount(
            count(UserStatus::cases()),
            $statusLabels,
            'UserStatus::labels() should contain all cases'
        );
        foreach (UserStatus::cases() as $case) {
            $this->assertArrayHasKey(
                $case->value,
                $statusLabels,
                "UserStatus::labels() should contain key for {$case->value}"
            );
        }
    }
}
