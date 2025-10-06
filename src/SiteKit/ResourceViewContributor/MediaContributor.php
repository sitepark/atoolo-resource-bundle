<?php

declare(strict_types=1);

namespace Atoolo\Resource\SiteKit\ResourceViewContributor;

use Atoolo\Resource\Resource;
use Atoolo\Resource\ResourceFeature\MediaFeature;
use Atoolo\Resource\ResourceViewBuilder;
use Atoolo\Resource\ResourceViewContributor;
use Atoolo\Resource\SiteKit\SiteKitResource;

class MediaContributor implements ResourceViewContributor
{
    public const MEDIA_OBJECT_TYPES = ['media', 'embedded_media'];

    public function supports(Resource $r): bool
    {
        return $r instanceof SiteKitResource
            && in_array($r->objectType, self::MEDIA_OBJECT_TYPES);
    }


    public function contribute(Resource $r, ResourceViewBuilder $b): void
    {
        if (!($r instanceof SiteKitResource)) {
            return;
        }

        $url = $r->data->getString('mediaUrl');
        $filename = basename($url);
        $filesize = $r->data->getInt('base.filesize', -1);
        $mimetype = $r->data->getString('base.mime');
        $format = $r->data->getString('base.format');

        $b->add(
            MediaFeature::class,
            fn() => new MediaFeature(
                $url,
                $filename,
                $filesize !== -1 ? $filesize : null,
                !empty($mimetype) ? $mimetype : null,
                !empty($format) ? $format : null,
            ),
        );
    }
}
