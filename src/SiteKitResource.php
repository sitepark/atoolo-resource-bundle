<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * Represents a resource from Sitepark's content management system
 */
class SiteKitResource extends Resource
{
    public function __construct(
        string $location,
        string $id,
        public readonly string $name,
        public readonly string $objectType,
        ResourceLanguage $lang,
        public readonly DataBag $data,
    ) {
        parent::__construct(
            $location,
            $id,
            $name,
            $objectType,
            $lang,
            $data,
        );
    }

    public function toLocation(): ResourceLocation
    {
        return ResourceLocation::of($this->location, $this->lang);
    }
}
