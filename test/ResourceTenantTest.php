<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test;

use Atoolo\Resource\DataBag;
use Atoolo\Resource\ResourceTenant;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResourceTenant::class)]
class ResourceTenantTest extends TestCase
{
    public function testCreateSetsIdFromString(): void
    {
        $tenant = ResourceTenant::create(['id' => '99']);
        $this->assertEquals('99', $tenant->id, 'id should be set from data id key');
    }

    public function testCreateSetsIdFromInt(): void
    {
        $tenant = ResourceTenant::create(['id' => 99]);
        $this->assertEquals('99', $tenant->id, 'integer id should be cast to string');
    }

    public function testCreateSetsName(): void
    {
        $tenant = ResourceTenant::create(['name' => 'My Tenant']);
        $this->assertEquals('My Tenant', $tenant->name, 'name should be set from data name key');
    }

    public function testCreateSetsAnchor(): void
    {
        $tenant = ResourceTenant::create(['anchor' => 'my-tenant']);
        $this->assertEquals('my-tenant', $tenant->anchor, 'anchor should be set from data anchor key');
    }

    public function testCreateSetsHost(): void
    {
        $tenant = ResourceTenant::create(['host' => 'example.com']);
        $this->assertEquals('example.com', $tenant->host, 'host should be set from data host key');
    }

    public function testCreateSetsAttributes(): void
    {
        $tenant = ResourceTenant::create(['attributes' => ['foo' => 'bar']]);
        $this->assertEquals(
            new DataBag(['foo' => 'bar']),
            $tenant->attributes,
            'attributes should be wrapped in a DataBag',
        );
    }

    public function testCreateUsesDefaultsForMissingKeys(): void
    {
        $tenant = ResourceTenant::create([]);
        $this->assertEquals('', $tenant->id, 'id should default to empty string');
    }
}
