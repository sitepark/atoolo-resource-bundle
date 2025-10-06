<?php

declare(strict_types=1);

namespace Atoolo\Resource\SiteKit\ResourceViewContributor;

use Atoolo\Resource\Model\Image\Image;
use Atoolo\Resource\Model\Image\ImageSource;
use Atoolo\Resource\Resource;
use Atoolo\Resource\ResourceFeature\TeaserFeature;
use Atoolo\Resource\ResourceViewBuilder;
use Atoolo\Resource\ResourceViewContributor;
use Atoolo\Resource\SiteKit\SiteKitResource;

class TeaserContributor implements ResourceViewContributor
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
            TeaserFeature::class,
            fn() => new TeaserFeature(
                $this->getHeadline($r),
                $this->getKicker($r),
                $this->getText($r),
                $this->getImages($r),
            ),
        );
    }

    private function getHeadline(SiteKitResource $resource): ?string
    {
        $headline = $resource->data->getString(
            'base.teaser.headline',
        );
        return !empty($headline) ? $headline : null;
    }

    private function getKicker(SiteKitResource $resource): ?string
    {
        $kickerText = $resource->data->getString(
            'base.teaser.kicker',
            $resource->data->getString('base.kicker'),
        );
        return !empty($kickerText) ? $kickerText : null;
    }

    private function getText(SiteKitResource $resource): ?string
    {
        $text = $resource->data->getString(
            'base.teaser.text',
        );
        return !empty($text) ? $text : null;
    }

    /**
     * @return array<string,Image>
     */
    private function getImages(SiteKitResource $resource): array
    {
        // TODO
        $imageData = $resource->data->getAssociativeArray(
            'base.teaser.image',
        );
        if (empty($imageData)) {
            return [];
        }
        $images = [];
        return $images;
    }
}
