<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Service;

use Atoolo\Resource\Service\FixedDecimalGroupingIdPathMapper;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FixedDecimalGroupingIdPathMapper::class)]
class FixedDecimalGroupingIdPathMapperTest extends TestCase
{
    private FixedDecimalGroupingIdPathMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new FixedDecimalGroupingIdPathMapper(2, 3);
    }

    public function testConstructorThrowsForNegativeLevels(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FixedDecimalGroupingIdPathMapper(-1, 3);
    }

    public function testConstructorThrowsForZeroDigitsPerLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FixedDecimalGroupingIdPathMapper(2, 0);
    }

    public function testEnabledReturnsTrue(): void
    {
        $this->assertTrue(
            $this->mapper->enabled(),
            'FixedDecimalGroupingIdPathMapper::enabled() should always return true',
        );
    }

    public function testPathForZero(): void
    {
        $this->assertEquals(
            '000/000/000',
            $this->mapper->pathFor(0),
            'pathFor(0) should return 000/000/000',
        );
    }

    public function testPathForOneThousand(): void
    {
        $this->assertEquals(
            '000/001/000',
            $this->mapper->pathFor(1000),
            'pathFor(1000) should return 000/001/000',
        );
    }

    public function testPathForMaxValue(): void
    {
        $this->assertEquals(
            '999/999/999',
            $this->mapper->pathFor(999999999),
            'pathFor(999999999) should return 999/999/999',
        );
    }

    public function testPathForThrowsForNegativeId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->mapper->pathFor(-1);
    }

    public function testPathForThrowsWhenCapacityExceeded(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->mapper->pathFor(1000000000);
    }

    public function testEmbeddedMediaPathFor(): void
    {
        $this->assertEquals(
            '000/001/000.media/32813',
            $this->mapper->embeddedMediaPathFor(1000, 32813),
            'embeddedMediaPathFor(1000, 32813) should return 000/001/000.media/32813',
        );
    }

    public function testMapToIdPathAlreadyMappedMediaUrl(): void
    {
        $this->assertEquals(
            '000/015/112.media/32813',
            $this->mapper->mapToIdPath('/000/015/112.media/32813.php'),
            'Already-mapped media-url should be returned without leading slash and without extension',
        );
    }

    public function testMapToIdPathAlreadyMappedUrl(): void
    {
        $this->assertEquals(
            '000/001/000',
            $this->mapper->mapToIdPath('/000/001/000.php'),
            'Already-mapped url should be returned without leading slash and without extension',
        );
    }

    public function testMapToIdPathResourceUrl(): void
    {
        $this->assertEquals(
            '000/001/000',
            $this->mapper->mapToIdPath('/dir/filename-1000'),
            'Resource-url with id suffix should be mapped to id path',
        );
    }

    public function testMapToIdPathMediaUrl(): void
    {
        $this->assertEquals(
            '000/001/000',
            $this->mapper->mapToIdPath('/dir/1000/filename.pdf'),
            'Media-url with id directory segment should be mapped to id path',
        );
    }

    public function testMapToIdPathEmbeddedMediaUrl(): void
    {
        $this->assertEquals(
            '000/001/000.media/32813',
            $this->mapper->mapToIdPath('/dir/1000-32813/filename.pdf'),
            'Embedded-media-url with container and media id should be mapped to embedded media path',
        );
    }

    public function testMapToIdPathUnrecognizedUrlReturnsNull(): void
    {
        $this->assertNull(
            $this->mapper->mapToIdPath('/some/unknown'),
            'Unrecognized url should return null',
        );
    }
}
