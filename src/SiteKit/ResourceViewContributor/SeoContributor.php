<?php

declare(strict_types=1);

namespace Atoolo\Resource\SiteKit\ResourceViewContributor;

use Atoolo\Resource\Resource;
use Atoolo\Resource\ResourceFeature\SeoFeature;
use Atoolo\Resource\ResourceViewBuilder;
use Atoolo\Resource\ResourceViewContributor;
use Atoolo\Resource\SiteKit\SiteKitResource;

class SeoContributor implements ResourceViewContributor
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

        /** @var string[] $keywords */
        $keywords = $r->data->getArray('metadata.keywords');

        $b->add(
            SeoFeature::class,
            fn() => new SeoFeature(
                $r->data->getString('base.title'),
                $r->data->getString('metadata.description'),
                $r->location, // TODO: Canonical URL, UrlRewriter?,
                $keywords,
                $r->data->getBool('noIndex') ? ['noindex'] : [],
            ),
        );
    }
}
