<?php

declare(strict_types=1);

namespace Atoolo\Resource;

use RuntimeException;

/**
 * @phpstan-type ContextPhp array{
 *     publisher: array{
 *          id: int,
 *          name: string,
 *          anchor: string,
 *          serverName: string,
 *          preview: bool,
 *          nature: string,
 *          locale: ?string,
 *          encoding: ?string,
 *          translationLocales: ?string[]
 *     }
 * }
 */
class SiteKitResourceChannelFactory implements ResourceChannelFactory
{
    private string $contextPhpFile;

    private string $resourceDir;

    private string $configDir;

    public function __construct(
        private readonly string $baseDir,
    ) {
        if (empty($this->baseDir)) {
            throw new RuntimeException(
                'RESOURCE_ROOT not set',
            );
        }
    }

    public function create(): ResourceChannel
    {

        $this->determinePaths();

        $data = $this->loadContextPhpFile();

        $searchIndex = str_replace(
            '.',
            '-',
            $data['publisher']['anchor'],
        );
        return new ResourceChannel(
            (string) $data['publisher']['id'],
            $data['publisher']['name'],
            $data['publisher']['anchor'],
            $data['publisher']['serverName'],
            $data['publisher']['preview'],
            $data['publisher']['nature'] ?? 'internet',
            $data['publisher']['locale'] ?? 'de_DE',
            $this->baseDir,
            $this->resourceDir,
            $this->configDir,
            $searchIndex,
            $data['publisher']['translationLocales'] ?? [],
        );
    }

    /**
     * @return ContextPhp
     */
    private function loadContextPhpFile(): array
    {
        $context = require $this->contextPhpFile;
        if (!is_array($context)) {
            throw new RuntimeException(
                'context.php must return an array',
            );
        }

        /** @var ContextPhp $context */
        return $context;
    }

    private function determinePaths(): void
    {
        $resourceLayoutContextPhpFile = $this->baseDir . '/context.php';

        if (file_exists($resourceLayoutContextPhpFile)) {
            $this->contextPhpFile = $resourceLayoutContextPhpFile;
            $this->resourceDir = $this->baseDir . '/objects';
            $this->configDir = $this->baseDir . '/configs';
            return;
        }

        $documentRootLayoutContextPhpFile =
            $this->baseDir . '/WEB-IES/context.php';

        if (!file_exists($documentRootLayoutContextPhpFile)) {
            throw new RuntimeException(
                'context.php does not exists: ' .
                $resourceLayoutContextPhpFile . ' or ' .
                $documentRootLayoutContextPhpFile,
            );
        }

        $this->contextPhpFile = $documentRootLayoutContextPhpFile;
        $this->resourceDir = $this->baseDir;
        $this->configDir = $this->baseDir;
    }
}
