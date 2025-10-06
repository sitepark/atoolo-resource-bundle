<?php

namespace Atoolo\Resource\Model\OpenGraph;

final class OpenGraphData
{
    /**
     * @param array<string,mixed> $additionalAttributes e.g. namespace specific attributes
     */
    public function __construct(
        public readonly string $title,
        public readonly string $type,
        public readonly OpenGraphImage $image,
        public readonly string $url,
        public readonly ?OpenGraphAudio $audio = null,
        public readonly ?string $description = null,
        public readonly ?string $determiner = null,
        public readonly ?string $locale = null,
        public readonly ?string $locale_alternate = null,
        public readonly ?string $site_name = null,
        public readonly ?OpenGraphVideo $video = null,
        public readonly array $additionalAttributes = [],
    ) {}
}
