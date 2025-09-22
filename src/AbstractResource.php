<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * Represents a resource with basic metadata (location, ID, name, language).
 *
 * This is the minimal domain object that contributors will use
 * to decide which features to provide for a resource view.
 */
class AbstractResource
{
    public function __construct(
        public readonly string $location,
        public readonly string $id,
        public readonly string $name,
        public readonly ResourceLanguage $lang,
    ) {}

    public function toLocation(): ResourceLocation
    {
        return ResourceLocation::of($this->location, $this->lang);
    }
}
