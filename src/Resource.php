<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * In the Atoolo context, resources are aggregated data from
 * IES (Sitepark's content management system).
 */
class Resource
{
    public function __construct(
        public readonly string $location,
        public readonly string $url,
        public readonly string $id,
        public readonly string $name,
        public readonly string $objectType,
        public readonly ResourceLanguage $lang,
        public readonly DataBag $data,
    ) {}

    /**
     * This factory method is primarily intended for use in tests.
     *
     * @param array{location?: string, url?: string, id?: string|int, name?: string, objectType?: string, locale?: string} $data
     */
    public static function create(array $data): self
    {
        return new Resource(
            $data['location'] ?? $data['url'] ?? '',
            $data['url'] ?? '',
            (string) ($data['id'] ?? ''),
            $data['name'] ?? '',
            $data['objectType'] ?? '',
            ResourceLanguage::of($data['locale'] ?? ''),
            new DataBag($data),
        );
    }

    public function toLocation(): ResourceLocation
    {
        return ResourceLocation::of($this->location, $this->lang);
    }
}
