<?php

namespace Evgeek\Tests\Unit\Api\Query\Segments\Methods\Nested;

use Evgeek\Moysklad\Api\Query\Segments\Methods\Nested\AuditSegment;
use Evgeek\Moysklad\Dictionaries\Segment;
use Evgeek\Tests\Unit\Api\Query\Segments\SegmentTestCase;

/**
 * @covers \Evgeek\Moysklad\Api\Query\Segments\Methods\Nested\AuditSegment
 */
class AuditSegmentTest extends SegmentTestCase
{
    protected string $builderClass = AuditSegment::class;

    public static function methodsWithCorrespondingSegmentClass(): array
    {
        return [];
    }

    public function testMethodReturnsCorrectClass(string $method = '', string $expectedSegment = '', string $expectedSegmentClass = '', array $parent = []): void
    {
        $this->markTestSkipped('AuditSegment has no nested segments to test.');
    }

    public function testSegmentConstant(): void
    {
        $this->assertSame(Segment::AUDIT, AuditSegment::SEGMENT);
    }
}
