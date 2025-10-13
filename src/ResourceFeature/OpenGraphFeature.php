<?php

namespace Atoolo\Resource\ResourceFeature;

use Atoolo\Resource\Model\OpenGraph\OpenGraphData;

/**
 * Provides the open graph data of a resource
 */
final class OpenGraphFeature implements ResourceFeature
{
    public function __construct(
        public readonly OpenGraphData $openGraphData,
    ) {}
}
