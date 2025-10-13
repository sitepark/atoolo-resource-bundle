<?php

declare(strict_types=1);

namespace Atoolo\Resource\SiteKit;

use Atoolo\Resource\DataBag;
use Atoolo\Resource\Resource;
use Atoolo\Resource\ResourceLanguage;
use Atoolo\Resource\ResourceLocation;

/**
 * Represents a resource from Sitepark's content management system
 * @property-read string $name name of the resource (e.g. in Infosite)
 * @property-read string $objectType objectType
 * @property-read DataBag $data aggregated data
 */
class SiteKitResource extends Resource
{
    public function __construct(
        string $location,
        string $id,
        string $name,
        string $objectType,
        ResourceLanguage $lang,
        DataBag $data,
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
