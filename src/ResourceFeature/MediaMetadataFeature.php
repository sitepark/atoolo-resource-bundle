<?php

namespace Atoolo\Resource\ResourceFeature;

/**
 * If a resource represents a media file, this feature provides
 * the metadata for said file
 */
final class MediaMetadataFeature implements ResourceFeature
{
    /**
     * @param string $url url to the media file
     * @param string $filename filename of the media file
     * @param ?int $size size in bytes of the media file
     * @param ?string $mimetype MIME type of the media
     * @param ?string $format file format of the media
     */
    public function __construct(
        public readonly string $url,
        public readonly string $filename,
        public readonly ?int $size = null,
        public readonly ?string $mimetype = null,
        public readonly ?string $format = null,
    ) {}
}
