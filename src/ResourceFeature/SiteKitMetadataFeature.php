<?php

namespace Atoolo\Resource\ResourceFeature;

/**
 * Sollte in eigenes Bundle
 */
final class SiteKitMetadataFeature implements ResourceFeature
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $anchor = null,
        // TODO usecase getArray('contentSectionTypes')
        // TODO usecase getInt('siteGroup.id') aus atoolo-search-bundle
        // mehr...
    ) {}
}
