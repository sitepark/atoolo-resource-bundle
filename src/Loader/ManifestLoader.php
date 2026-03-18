<?php

declare(strict_types=1);

namespace Atoolo\Resource\Loader;

use Atoolo\Resource\Manifest;
use Atoolo\Resource\ResourceChannel;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsAlias(id: 'atoolo_resource.manifest_loader')]
class ManifestLoader
{
    public function __construct(
        #[Autowire(service: 'atoolo_resource.resource_channel')]
        private readonly ResourceChannel $resourceChannel,
    ) {}

    public function load(string $pathInfo): ?Manifest
    {
        $manifestFile = $this->getManifestFile($pathInfo);
        if (!file_exists($manifestFile)) {
            return null;
        }
        return $this->deserialize($manifestFile);
    }

    public function deserialize(string $manifestFile): ?Manifest
    {
        $saveErrorReporting = error_reporting();

        try {
            error_reporting(E_ERROR | E_PARSE);
            ob_start();
            $data = require $manifestFile;
            if (!is_array($data)) {
                throw new RuntimeException(
                    'The SiteManifest configuration ' .
                    $manifestFile . ' should return an array',
                );
            }
            return new Manifest(
                $data['home'] ?? 0,
                $data['errors'] ?? [],
            );
        } finally {
            ob_end_clean();
            error_reporting($saveErrorReporting);
        }
    }

    private function getManifestFile(string $path): string
    {
        $configBaseDir = $this->resourceChannel->configDir;

        if ($path === '/') {
            return $configBaseDir . '/manifest.php';
        }

        return $configBaseDir . $path . '/manifest.php';
    }
}
