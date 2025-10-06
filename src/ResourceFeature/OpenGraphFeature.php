<?php

namespace Atoolo\Resource\ResourceFeature;

use Atoolo\Resource\Model\OpenGraph\OpenGraphData;

/**
 * Feature for open graph data
 */
final class OpenGraphFeature implements ResourceFeature
{
    public function __construct(
        public readonly OpenGraphData $openGraphData,
    ) {}
}
