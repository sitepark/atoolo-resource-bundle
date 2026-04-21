<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Service;

use Atoolo\Resource\Service\NoopIdPathMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(NoopIdPathMapper::class)]
class NoopIdPathMapperTest extends TestCase
{
    private NoopIdPathMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new NoopIdPathMapper();
    }

    public function testEnabledReturnsFalse(): void
    {
        $this->assertFalse(
            $this->mapper->enabled(),
            'NoopIdPathMapper::enabled() should always return false',
        );
    }

    public function testPathForThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->mapper->pathFor(1);
    }

    public function testMapToIdPathThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->mapper->mapToIdPath('/url');
    }

    public function testEmbeddedMediaPathForThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->mapper->embeddedMediaPathFor(1, 2);
    }
}
