<?php

declare(strict_types=1);

namespace Atoolo\Resource\ResourceFeature;

/**
 * Provides data about the publishing state of a resource
 */
final class PublishingDataFeature implements ResourceFeature
{
    public function __construct(
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
        public readonly ?\DateTimeImmutable $publishedAt = null,
    ) {}
}
