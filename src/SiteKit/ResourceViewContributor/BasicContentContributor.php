<?php

declare(strict_types=1);

namespace Atoolo\Resource\SiteKit\ResourceViewContributor;

use Atoolo\Resource\Model\Image;
use Atoolo\Resource\Resource;
use Atoolo\Resource\ResourceFeature\BasicContentFeature;
use Atoolo\Resource\ResourceFeature\TeaserFeature;
use Atoolo\Resource\ResourceHierarchyWalker;
use Atoolo\Resource\ResourceViewBuilder;
use Atoolo\Resource\ResourceViewContributor;
use Atoolo\Resource\SiteKit\SiteKitResource;
use DateTimeImmutable;

class BasicContentContributor implements ResourceViewContributor
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
            BasicContentFeature::class,
            fn() => new BasicContentFeature(
                $this->getHeadline($r),
                $this->getSubheadline($r),
                $this->getKicker($r),
                $this->getIntro($r),
                $this->getDate($r),
            ),
        );
    }

    private function getHeadline(SiteKitResource $resource): ?string
    {
        $headline = $resource->data->getString(
            'metadata.headline',
        );
        return !empty($headline) ? $headline : null;
    }

    private function getSubheadline(SiteKitResource $resource): ?string
    {
        $subheadline = $resource->data->getString(
            'metadata.subheadline',
        );
        return !empty($subheadline) ? $subheadline : null;
    }

    private function getKicker(SiteKitResource $resource): ?string
    {
        $kickerText = $resource->data->getString(
            'base.teaser.kicker',
            $resource->data->getString('base.kicker'),
        );
        return !empty($kickerText) ? $kickerText : null;
    }

    private function getIntro(SiteKitResource $resource): ?string
    {
        $introText = $resource->data->getString(
            'metadata.intro',
        );
        return !empty($introText) ? $introText : null;
    }

    private function getDate(SiteKitResource $resource): ?DateTimeImmutable
    {
        $date = $resource->data->getInt(
            'base.date',
            -1,
        );
        return $date !== -1 ? (new \DateTimeImmutable())->setTimestamp($date) : null;
    }
}
