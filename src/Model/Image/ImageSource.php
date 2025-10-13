<?php

declare(strict_types=1);

namespace Atoolo\Resource\Model\Image;

/**
 * Represents a single source for a responsive image, corresponding to a
 * <source> tag or an entry in a srcset attribute.
 */
final class ImageSource
{
    /**
     * @param string $url The URL of this image version.
     * @param ?string $mediaQuery The media query for art direction (e.g., '(min-width: 900px)').
     * @param ?string $mimeType The MIME type for different formats (e.g., 'image/webp').
     * @param ?int $width The width of this version in pixels.
     * @param ?int $height The height of this version in pixels.
     */
    public function __construct(
        public readonly string $url,
        public readonly ?string $mediaQuery = null,
        public readonly ?string $mimeType = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
    ) {}
}
