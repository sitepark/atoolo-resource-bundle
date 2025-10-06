<?php

namespace Atoolo\Resource\ResourceFeature;

use Atoolo\Resource\Model\Image\Image;

/**
 * Feature for teaser data
 */
final class TeaserFeature implements ResourceFeature
{
    /**
     * @param array<string,Image> $images teaser images keyed by their role
     */
    public function __construct(
        public readonly ?string $headline = null,
        public readonly ?string $kicker = null,
        public readonly ?string $text = null,
        public readonly array $images = [],
    ) {}
}
