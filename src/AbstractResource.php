<?php

declare(strict_types=1);

namespace Atoolo\Resource;

abstract class AbstractResource
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
