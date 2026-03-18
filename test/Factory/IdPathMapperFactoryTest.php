<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Factory;

use Atoolo\Resource\DataBag;
use Atoolo\Resource\Factory\IdPathMapperFactory;
use Atoolo\Resource\ResourceChannel;
use Atoolo\Resource\ResourceTenant;
use Atoolo\Resource\Service\FixedDecimalGroupingIdPathMapper;
use Atoolo\Resource\Service\NoopIdPathMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IdPathMapperFactory::class)]
class IdPathMapperFactoryTest extends TestCase
{
    public function testCreateReturnsFixedDecimalGroupingIdPathMapperWhenResourcePathTypeIsId(): void
    {
        $factory = new IdPathMapperFactory(
            $this->createResourceChannel(['resourcePathType' => 'id']),
        );

        $this->assertInstanceOf(
            FixedDecimalGroupingIdPathMapper::class,
            $factory->create(),
            'create() should return FixedDecimalGroupingIdPathMapper when resourcePathType is "id"',
        );
    }

    public function testCreateReturnsNoopIdPathMapperWhenResourcePathTypeIsNotId(): void
    {
        $factory = new IdPathMapperFactory(
            $this->createResourceChannel([]),
        );

        $this->assertInstanceOf(
            NoopIdPathMapper::class,
            $factory->create(),
            'create() should return NoopIdPathMapper when resourcePathType is not "id"',
        );
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function createResourceChannel(array $attributes): ResourceChannel
    {
        return new ResourceChannel(
            id: '',
            name: '',
            anchor: '',
            serverName: '',
            isPreview: false,
            nature: '',
            locale: '',
            baseDir: '',
            resourceDir: '',
            configDir: '',
            searchIndex: '',
            translationLocales: [],
            attributes: new DataBag($attributes),
            tenant: $this->createStub(ResourceTenant::class),
        );
    }
}
