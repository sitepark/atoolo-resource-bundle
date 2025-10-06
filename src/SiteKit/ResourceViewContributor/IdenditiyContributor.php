<?php

declare(strict_types=1);

namespace Atoolo\Resource\SiteKit\ResourceViewContributor;

use Atoolo\Resource\Resource;
use Atoolo\Resource\ResourceFeature\IdentityFeature;
use Atoolo\Resource\ResourceViewBuilder;
use Atoolo\Resource\ResourceViewContributor;
use Atoolo\Resource\SiteKit\SiteKitResource;

class IdenditiyContributor implements ResourceViewContributor
{
    public const string ID_PREFIX = 'SiteKit-';

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
            IdentityFeature::class,
            fn() => new IdentityFeature(
                self::ID_PREFIX . $r->id, // prefix to ensure global uniqueness
                $r->location, // TODO: should be absolute URL (UrlRewriter)!
            ),
        );
    }
}
