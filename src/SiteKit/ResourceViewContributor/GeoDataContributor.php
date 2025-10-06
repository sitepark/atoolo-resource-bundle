<?php

declare(strict_types=1);

namespace Atoolo\Resource\SiteKit\ResourceViewContributor;

use Atoolo\Resource\Resource;
use Atoolo\Resource\ResourceFeature\GeoDataFeature;
use Atoolo\Resource\ResourceViewBuilder;
use Atoolo\Resource\ResourceViewContributor;
use Atoolo\Resource\SiteKit\SiteKitResource;

class GeoDataContributor implements ResourceViewContributor
{
    public function supports(Resource $r): bool
    {
        return $r instanceof SiteKitResource && $r->data->has('geo');
    }

    public function contribute(Resource $r, ResourceViewBuilder $b): void
    {
        if (!($r instanceof SiteKitResource)) {
            return;
        }

        $b->add(
            GeoDataFeature::class,
            fn() => new GeoDataFeature(
                $r->data->getAssociativeArray('geo'),
            ),
        );
    }
}
