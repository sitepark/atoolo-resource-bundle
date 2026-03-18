<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Loader;

use Atoolo\Resource\DataBag;
use Atoolo\Resource\Loader\ManifestLoader;
use Atoolo\Resource\Manifest;
use Atoolo\Resource\ResourceChannel;
use Atoolo\Resource\ResourceTenant;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(ManifestLoader::class)]
class ManifestLoaderTest extends TestCase
{
    private ManifestLoader $loader;

    private string $configDir;

    protected function setUp(): void
    {
        $this->configDir = realpath(
            __DIR__ . '/../resources/Loader/ManifestLoader/config',
        );
        $this->loader = new ManifestLoader(
            $this->createResourceChannel($this->configDir),
        );
    }

    public function testLoadReturnsNullWhenManifestFileNotExists(): void
    {
        $this->assertNull(
            $this->loader->load('/nonexistent'),
            'load() should return null if no manifest.php exists at the given path',
        );
    }

    public function testLoadReturnsManifestForRootPath(): void
    {
        $this->assertEquals(
            new Manifest(42, ['404' => 99, '500' => 100]),
            $this->loader->load('/'),
            'load("/") should read manifest.php directly from configDir',
        );
    }

    public function testLoadReturnsManifestForSubPath(): void
    {
        $this->assertEquals(
            new Manifest(7, []),
            $this->loader->load('/sub'),
            'load("/sub") should read manifest.php from configDir/sub/',
        );
    }

    public function testDeserializeThrowsRuntimeExceptionWhenFileDoesNotReturnArray(): void
    {
        $invalidFile = realpath(
            __DIR__ . '/../resources/Loader/ManifestLoader/configInvalid/manifest.php',
        );

        $this->expectException(RuntimeException::class);

        $this->loader->deserialize($invalidFile);
    }

    private function createResourceChannel(string $configDir): ResourceChannel
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
            configDir: $configDir,
            searchIndex: '',
            translationLocales: [],
            attributes: new DataBag([]),
            tenant: $this->createStub(ResourceTenant::class),
        );
    }
}
