<?php

namespace Atoolo\Resource\ResourceFeature;

/**
 * Provides the content data of a resource
 */
final class ContentFeature implements ResourceFeature
{
    public function __construct(
        public readonly ?string $headline = null,
        public readonly ?string $subheadline = null,
        public readonly ?string $kicker = null,
        public readonly ?string $intro = null,
        public readonly ?\DateTimeImmutable $date = null,
        // ...content? TODO
    ) {}
}
