<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Factory;

use Atoolo\Resource\DataBag;
use Atoolo\Resource\Factory\SiteKitResourceChannelFactory;
use Atoolo\Resource\ResourceChannel;
use Atoolo\Resource\ResourceTenant;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SiteKitResourceChannelFactory::class)]
class SiteKitResourceChannelFactoryTest extends TestCase
{
    public function testCreateWithResourceLayout(): void
    {
        $baseDir = __DIR__ .
            '/../resources/SiteKitResourceChannelFactory' .
            '/resourceLayout';
        $resourceDir = $baseDir . '/objects';
        $configDir = $baseDir . '/configs';

        $factory = new SiteKitResourceChannelFactory($baseDir);
        $channel = $factory->create();

        $expected = ResourceChannel::create([
            'id' => '1',
            'name' => 'Test',
            'anchor' => 'test',
            'serverName' => 'www.test.org',
            'isPreview' => true,
            'nature' => 'internet',
            'locale' => 'de_DE',
            'baseDir' => $baseDir,
            'resourceDir' => $resourceDir,
            'configDir' => $configDir,
            'searchIndex' => 'test',
            'attributes' => [
                'abc' => 'xyz' ,
                'hello' => 'world',
            ],
            'tenant' => [
                'id' => '2',
                'name' => 'Test-Tanent',
                'anchor' => 'test-tanent',
                'host' => 'test-host',
                'attributes' => [
                    'abc' => 'cde',
                ],
            ],
        ]);
        $this->assertEquals(
            $expected,
            $channel,
            'ResourceChannel does not match expected values',
        );
    }

    public function testCreateWithDocumentRootLayout(): void
    {
        $baseDir = __DIR__ .
            '/../resources/SiteKitResourceChannelFactory' .
            '/documentRootLayout';
        $resourceDir = $baseDir;
        $configDir = $baseDir;

        $factory = new SiteKitResourceChannelFactory($baseDir);
        $channel = $factory->create();

        $expected = ResourceChannel::create([
            'id' => '1',
            'name' => 'Test',
            'anchor' => 'test',
            'serverName' => 'www.test.org',
            'isPreview' => true,
            'nature' => 'internet',
            'locale' => 'de_DE',
            'baseDir' => $baseDir,
            'resourceDir' => $resourceDir,
            'configDir' => $configDir,
            'searchIndex' => 'test',
            'attributes' => [
                'abc' => 'cde',
            ],
            'tenant' => [
                'id' => '2',
                'name' => 'Test-Tanent',
                'anchor' => 'test-tanent',
                'host' => 'test-host',
                'attributes' => [
                    'abc' => 'cde',
                ],
            ],
        ]);
        $this->assertEquals(
            $expected,
            $channel,
            'ResourceChannel does not match expected values',
        );
    }

    public function testCreateNonExistsContextPhp(): void
    {
        $resourceDir = __DIR__ .
            '/../resources/SiteKitResourceChannelFactory' .
            '/noexists';

        $factory = new SiteKitResourceChannelFactory($resourceDir);

        $this->expectException(\RuntimeException::class);
        $factory->create();
    }

    public function testCreateWithInvalidContextPhp(): void
    {
        $resourceDir = __DIR__ .
            '/../resources/SiteKitResourceChannelFactory' .
            '/invalid';

        $factory = new SiteKitResourceChannelFactory($resourceDir);

        $this->expectException(\RuntimeException::class);
        $factory->create();
    }

    public function testEmptyBaseDIr(): void
    {
        $this->expectException(\RuntimeException::class);
        new SiteKitResourceChannelFactory('');
    }
}
