<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * Resources are aggregated by the IES (Sitepark's content management system)
 * into different channels. This can be the live channel for the
 * website, but also a preview or intranet channel, for example.
 *
 * These channels have certain properties that are mapped in this class.
 */
class ResourceChannel
{
    /**
     * @param string[] $translationLocales
     * @codeCoverageIgnore
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $anchor,
        public readonly string $serverName,
        public readonly bool $isPreview,
        public readonly string $nature,
        public readonly string $locale,
        public readonly string $baseDir,
        public readonly string $resourceDir,
        public readonly string $configDir,
        public readonly string $searchIndex,
        public readonly array $translationLocales,
        public readonly DataBag $attributes,
        public readonly ResourceTenant $tenant,
    ) {}

    /**
     * This factory method is primarily intended for use in tests.

     * @param array{
     *     id?: string|int,
     *     name?: string,
     *     anchor?: string,
     *     serverName?: string,
     *     isPreview?: bool,
     *     nature?: string,
     *     locale?: string,
     *     baseDir?: string,
     *     resourceDir?: string,
     *     configDir?: string,
     *     searchIndex?: string,
     *     translationLocales?: string[],
     *     attributes?: array<string, mixed>,
     *     tenant?: array{id?: string|int, name?: string, anchor?: string, host?: string, attributes?: array<string, mixed>},
     * } $data
     */
    public static function create(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            $data['name'] ?? '',
            $data['anchor'] ?? '',
            $data['serverName'] ?? '',
            $data['isPreview'] ?? false,
            $data['nature'] ?? '',
            $data['locale'] ?? '',
            $data['baseDir'] ?? '',
            $data['resourceDir'] ?? '',
            $data['configDir'] ?? '',
            $data['searchIndex'] ?? '',
            $data['translationLocales'] ?? [],
            new DataBag($data['attributes'] ?? []),
            ResourceTenant::create($data['tenant'] ?? []),
        );
    }
}
