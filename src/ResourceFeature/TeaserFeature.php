<?php

namespace Atoolo\Resource\ResourceFeature;

use Atoolo\Resource\Model\Image\Image;

/**
 * Provides teaser data of a resource
 */
final class TeaserFeature implements ResourceFeature
{
    public function __construct(
        public readonly ?string $headline = null,
        public readonly ?string $kicker = null,
        public readonly ?string $text = null,
        public readonly ?Image $image = null,
    ) {}
}
