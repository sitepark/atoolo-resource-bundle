<?php

declare(strict_types=1);

namespace Atoolo\ResourceLoader\Test;

use Atoolo\ResourceLoader\Resource;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    public function testConstructor(): void
    {
        $resource = new Resource(
            'schema:path',
            '123',
            'Content-Page',
            'content'
        );
        $this->assertEquals(
            'schema:path',
            $resource->getLocation(),
            'unexpected location'
        );
        $this->assertEquals(
            '123',
            $resource->getId(),
            'unexpected id'
        );
        $this->assertEquals(
            'Content-Page',
            $resource->getName(),
            'unexpected name'
        );
        $this->assertEquals(
            'content',
            $resource->getObjectType(),
            'unexpected content'
        );
    }
}