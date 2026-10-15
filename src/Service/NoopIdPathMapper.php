<?php

declare(strict_types=1);

namespace Atoolo\Resource\Service;

use Exception;
use RuntimeException;

/**
 * The IdPathMapper service must be defined as a lazy service for the symfony bridge
 * bundle, as the channel service is not yet available during initialisation. However,
 * Symfony does not support optional lazy services. Therefore, there must always be a service.
 * This NoopIdPathMapper handles the case where no IdPathMapper is used.
 */
class NoopIdPathMapper implements IdPathMapper
{
    public function enabled(): bool
    {
        return false;
    }

    public function mapToIdPath(string $url): ?string
    {
        return throw new RuntimeException("Unsupported operation");
    }

    public function embeddedMediaPathFor(int $mediaContainerId, int $mediaId): string
    {
        return throw new RuntimeException("Unsupported operation");
    }

    public function pathFor(int $id): string
    {
        return throw new RuntimeException("Unsupported operation");
    }
}
