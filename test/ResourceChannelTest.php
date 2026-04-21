<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test;

use Atoolo\Resource\DataBag;
use Atoolo\Resource\ResourceChannel;
use Atoolo\Resource\ResourceTenant;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResourceChannel::class)]
class ResourceChannelTest extends TestCase
{
    public function testCreateSetsIdFromString(): void
    {
        $channel = ResourceChannel::create(['id' => '5']);
        $this->assertEquals('5', $channel->id, 'id should be set from data id key');
    }

    public function testCreateSetsIdFromInt(): void
    {
        $channel = ResourceChannel::create(['id' => 5]);
        $this->assertEquals('5', $channel->id, 'integer id should be cast to string');
    }

    public function testCreateSetsName(): void
    {
        $channel = ResourceChannel::create(['name' => 'Live']);
        $this->assertEquals('Live', $channel->name, 'name should be set from data name key');
    }

    public function testCreateSetsAnchor(): void
    {
        $channel = ResourceChannel::create(['anchor' => 'live']);
        $this->assertEquals('live', $channel->anchor, 'anchor should be set from data anchor key');
    }

    public function testCreateSetsServerName(): void
    {
        $channel = ResourceChannel::create(['serverName' => 'www.example.com']);
        $this->assertEquals('www.example.com', $channel->serverName, 'serverName should be set from data serverName key');
    }

    public function testCreateSetsIsPreview(): void
    {
        $channel = ResourceChannel::create(['isPreview' => true]);
        $this->assertTrue($channel->isPreview, 'isPreview should be set from data isPreview key');
    }

    public function testCreateSetsNature(): void
    {
        $channel = ResourceChannel::create(['nature' => 'internet']);
        $this->assertEquals('internet', $channel->nature, 'nature should be set from data nature key');
    }

    public function testCreateSetsLocale(): void
    {
        $channel = ResourceChannel::create(['locale' => 'de_DE']);
        $this->assertEquals('de_DE', $channel->locale, 'locale should be set from data locale key');
    }

    public function testCreateSetsBaseDir(): void
    {
        $channel = ResourceChannel::create(['baseDir' => '/var/www']);
        $this->assertEquals('/var/www', $channel->baseDir, 'baseDir should be set from data baseDir key');
    }

    public function testCreateSetsResourceDir(): void
    {
        $channel = ResourceChannel::create(['resourceDir' => '/var/www/objects']);
        $this->assertEquals('/var/www/objects', $channel->resourceDir, 'resourceDir should be set from data resourceDir key');
    }

    public function testCreateSetsConfigDir(): void
    {
        $channel = ResourceChannel::create(['configDir' => '/var/www/configs']);
        $this->assertEquals('/var/www/configs', $channel->configDir, 'configDir should be set from data configDir key');
    }

    public function testCreateSetsSearchIndex(): void
    {
        $channel = ResourceChannel::create(['searchIndex' => 'live-index']);
        $this->assertEquals('live-index', $channel->searchIndex, 'searchIndex should be set from data searchIndex key');
    }

    public function testCreateSetsTranslationLocales(): void
    {
        $channel = ResourceChannel::create(['translationLocales' => ['en_US', 'fr_FR']]);
        $this->assertEquals(
            ['en_US', 'fr_FR'],
            $channel->translationLocales,
            'translationLocales should be set from data translationLocales key',
        );
    }

    public function testCreateSetsAttributes(): void
    {
        $channel = ResourceChannel::create(['attributes' => ['key' => 'value']]);
        $this->assertEquals(
            new DataBag(['key' => 'value']),
            $channel->attributes,
            'attributes should be wrapped in a DataBag',
        );
    }

    public function testCreateSetsTenant(): void
    {
        $channel = ResourceChannel::create(['tenant' => ['id' => '1', 'name' => 'Acme']]);
        $this->assertEquals(
            ResourceTenant::create(['id' => '1', 'name' => 'Acme']),
            $channel->tenant,
            'tenant should be created from nested tenant array',
        );
    }

    public function testCreateUsesDefaultsForMissingKeys(): void
    {
        $channel = ResourceChannel::create([]);
        $this->assertFalse($channel->isPreview, 'isPreview should default to false');
    }
}
