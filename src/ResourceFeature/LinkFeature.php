<?php

namespace Atoolo\Resource\ResourceFeature;

use Atoolo\Resource\Model\Link;

/**
 * Provides a html-renderable link that references the underlying resource
 */
final class LinkFeature implements ResourceFeature
{
    public function __construct(
        public Link $link,
    ) {}
}
