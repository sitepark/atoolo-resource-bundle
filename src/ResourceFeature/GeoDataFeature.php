<?php

namespace Atoolo\Resource\ResourceFeature;

final class GeoDataFeature implements ResourceFeature
{
    /**
     * @param array<mixed> $geoData
     */
    public function __construct(
        public array $geoData, // TODO custom class for geodata
    ) {}
}
