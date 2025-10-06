<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * !!! This class will be made abstract in version 2.0.0.

 * Represents a resource with basic metadata (location, ID, language).
 */
class Resource
{
    /**
     * @deprecated This property will be removed in version 2.0.0.
     * Use `\SP\Resource\SiteKitResource` if you want to keep using this property.
     * @see \SP\Resource\SiteKitResource
     */
    public readonly string $name;

    /**
     * @deprecated This property will be removed in version 2.0.0.
     * Use `\SP\Resource\SiteKitResource` if you want to keep using this property.
     * @see \SP\Resource\SiteKitResource
     */
    public readonly string $objectType;

    /**
     * @deprecated This property will be removed in version 2.0.0.
     * Use `\SP\Resource\SiteKitResource` if you want to keep using this property.
     * @see \SP\Resource\SiteKitResource
     */
    public readonly DataBag $data;

    public function __construct(
        public readonly string $location,
        public readonly string $id,
        string $name,
        string $objectType,
        public readonly ResourceLanguage $lang,
        DataBag $data,
    ) {
        $this->name = $name;
        $this->objectType = $objectType;
        $this->data = $data;
    }

    /**
     * @deprecated This method will be made abstract in version 2.0.0.
     * Use the class \SP\Resource\SiteKitResource if you want to keep using this method.
     */
    public function toLocation(): ResourceLocation
    {
        return ResourceLocation::of($this->location, $this->lang);
    }
}
