<?php

namespace Atoolo\Resource\ResourceFeature;

use Atoolo\Resource\Model\GeoJson;

/**
 * Provides the geospatial data of a resource
 */
final class GeoFeature implements ResourceFeature
{
    public function __construct(
        public readonly GeoJson $geoData,
    ) {}
}
