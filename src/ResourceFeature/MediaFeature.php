<?php

namespace Atoolo\Resource\ResourceFeature;

/**
 * Basic attributes for when the underlying resource represents
 * a media file
 */
final class MediaFeature implements ResourceFeature
{
    public function __construct(
        public readonly string $url,
        public readonly string $filename,
        public readonly ?int $filesize = null,
        public readonly ?string $mimetype = null,
        public readonly ?string $format = null,
    ) {}
}
