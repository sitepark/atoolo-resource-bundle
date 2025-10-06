<?php

declare(strict_types=1);

namespace Atoolo\Resource\SiteKit\ResourceViewContributor;

use Atoolo\Resource\Resource;
use Atoolo\Resource\ResourceFeature\MediaFeature;
use Atoolo\Resource\ResourceFeature\PublishingDataFeature;
use Atoolo\Resource\ResourceViewBuilder;
use Atoolo\Resource\ResourceViewContributor;
use Atoolo\Resource\SiteKit\SiteKitResource;

class PublishingDataContributor implements ResourceViewContributor
{
    public function supports(Resource $r): bool
    {
        return $r instanceof SiteKitResource;
    }


    public function contribute(Resource $r, ResourceViewBuilder $b): void
    {
        if (!($r instanceof SiteKitResource)) {
            return;
        }

        $b->add(
            PublishingDataFeature::class,
            fn() => new PublishingDataFeature(
                (new \DateTimeImmutable())->setTimestamp(
                    $r->data->getInt('created'),
                ),
                (new \DateTimeImmutable())->setTimestamp(
                    $r->data->getInt('changed'),
                ),
                (new \DateTimeImmutable())->setTimestamp(
                    $r->data->getInt('generated'),
                ),
            ),
        );
    }
}
