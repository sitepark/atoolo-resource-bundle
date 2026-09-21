<?php

declare(strict_types=1);

namespace Atoolo\Resource\Service;

interface IdPathMapper
{
    public function enabled(): bool;

    public function pathFor(int $id): string;

    public function mapToIdPath(string $url): ?string;

    public function embeddedMediaPathFor(int $mediaContainerId, int $mediaId): string;
}
