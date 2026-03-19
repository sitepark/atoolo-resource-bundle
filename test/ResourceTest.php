<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test;

use Atoolo\Resource\Resource;
use Atoolo\Resource\ResourceLanguage;
use Atoolo\Resource\ResourceLocation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Resource::class)]
class ResourceTest extends TestCase
{
    public function testToLocation(): void
    {
        $resource = TestResourceFactory::create([
            'url' => 'path',
            'locale' => 'en_US',
        ]);
        $this->assertEquals(
            ResourceLocation::of(
                'path',
                \Atoolo\Resource\ResourceLanguage::of('en'),
            ),
            $resource->toLocation(),
            'unexpected data value',
        );
    }

    public function testCreateUsesLocationKey(): void
    {
        $resource = Resource::create(['location' => '/a', 'url' => '/b']);
        $this->assertEquals('/a', $resource->location, 'location key should take precedence over url');
    }

    public function testCreateFallsBackToUrlForLocation(): void
    {
        $resource = Resource::create(['url' => '/b']);
        $this->assertEquals('/b', $resource->location, 'url should be used as location when location key is absent');
    }

    public function testCreateSetsUrl(): void
    {
        $resource = Resource::create(['url' => '/my/url']);
        $this->assertEquals('/my/url', $resource->url, 'url should be set from data url key');
    }

    public function testCreateSetsIdFromString(): void
    {
        $resource = Resource::create(['id' => '42']);
        $this->assertEquals('42', $resource->id, 'id should be set from data id key');
    }

    public function testCreateSetsIdFromInt(): void
    {
        $resource = Resource::create(['id' => 42]);
        $this->assertEquals('42', $resource->id, 'integer id should be cast to string');
    }

    public function testCreateSetsName(): void
    {
        $resource = Resource::create(['name' => 'My Page']);
        $this->assertEquals('My Page', $resource->name, 'name should be set from data name key');
    }

    public function testCreateSetsObjectType(): void
    {
        $resource = Resource::create(['objectType' => 'article']);
        $this->assertEquals('article', $resource->objectType, 'objectType should be set from data objectType key');
    }

    public function testCreateSetsLangFromLocale(): void
    {
        $resource = Resource::create(['locale' => 'de_DE']);
        $this->assertEquals(ResourceLanguage::of('de'), $resource->lang, 'lang should be derived from locale key');
    }

    public function testCreateUsesDefaultsForMissingKeys(): void
    {
        $resource = Resource::create([]);
        $this->assertEquals('', $resource->location, 'location should default to empty string');
    }
}
