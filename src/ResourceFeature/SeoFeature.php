<?php

namespace Atoolo\Resource\ResourceFeature;

/**
 * Provides basic metadata/SEO data of a resource
 *
 * @phpstan-type RobotsDirective "index"|"noindex"|"follow"|"nofollow"|"all"|"none"|"noarchive"|"nosnippet"|"noimageindex"|"nocache"
 */
final class SeoFeature implements ResourceFeature
{
    /**
     * @param string $title Title of the resource, e.g. for <title>
     * @param ?string $description Meta description (150–160 chars recommended)
     * @param ?string $canonicalUrl Optional canonical URL for SEO / deduplication.
     * @param string[] $keywords Optional meta keywords
     * @param RobotsDirective[] $robots Fine-grained robots directives (e.g. ["noindex", "nofollow", "noarchive"])
     */
    public function __construct(
        public readonly string $title,
        public readonly ?string $description = null,
        public readonly ?string $canonicalUrl = null,
        public readonly array $keywords = [],
        public readonly array $robots = [],
    ) {}

    public function isNoIndex(): bool
    {
        return in_array('noindex', $this->robots);
    }
}
