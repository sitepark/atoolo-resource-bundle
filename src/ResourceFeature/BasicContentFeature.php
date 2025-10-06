<?php

namespace Atoolo\Resource\ResourceFeature;

/**
 * Feature for basic content data
 */
final class BasicContentFeature implements ResourceFeature
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
