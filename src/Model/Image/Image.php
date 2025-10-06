<?php

declare(strict_types=1);

namespace Atoolo\Resource\Model\Image;

use Atoolo\Resource\Model\Copyright;

/**
 * Represents a complete image, including multiple sources for responsive
 * design (<picture>, srcset) and essential metadata.
 */
final class Image
{
    /**
     * @param string $url The primary, fallback URL for the <img> src attribute.
     * @param ImageSource[] $sources An array of alternative sources for different formats and resolutions.
     * @param ?string $alt The essential alternative text for accessibility.
     * @param ?int $width The intrinsic width of the fallback image to prevent layout shift.
     * @param ?int $height The intrinsic height of the fallback image to prevent layout shift.
     * @param ?Copyright $copyright Copyright information for the image.
     * @param ?string $characteristic
     */
    public function __construct(
        public readonly string $url,
        public readonly array $sources = [],
        public readonly ?string $alt = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?Copyright $copyright = null,
        public readonly ?string $characteristic = null,
    ) {}
}
