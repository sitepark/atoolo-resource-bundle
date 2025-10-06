<?php

declare(strict_types=1);

namespace Atoolo\Resource\SiteKit\ResourceViewContributor;

use Atoolo\Resource\Model\Link;
use Atoolo\Resource\Resource;
use Atoolo\Resource\ResourceFeature\LinkFeature;
use Atoolo\Resource\ResourceViewBuilder;
use Atoolo\Resource\ResourceViewContributor;
use Atoolo\Resource\SiteKit\SiteKitResource;

class LinkContributor implements ResourceViewContributor
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
            LinkFeature::class,
            function () use ($r) {
                $isExternal = $this->isExternal($r);
                return new LinkFeature(
                    new Link(
                        $this->getUrl($r),
                        $this->getLabel($r),
                        $this->getAccessibilityLabel($r),
                        $this->getDescription($r),
                        $isExternal ? 'external' : null,
                        $this->opensNewWindow($r) ? '_blank' : null,
                        $isExternal,
                    ),
                );
            },
        );
    }

    protected function getUrl(SiteKitResource $resource): string
    {
        return $this->isMedia($resource)
            ? $resource->data->getString('mediaUrl')
            : $resource->location;
        // TODO: Use UrlRewriter
        /*
        return $this->isMedia($resource)
            ? $this->urlRewriter->rewrite(
                UrlRewriteType::MEDIA,
                $resource->data->getString('mediaUrl'),
                UrlRewriteOptions::builder()->lang($resource->lang->code)->build(),
            )
            : $this->urlRewriter->rewrite(
                UrlRewriteType::LINK,
                $resource->location,
                UrlRewriteOptions::builder()->lang($resource->lang->code)->build(),
            );
        */
    }

    protected function getLabel(SiteKitResource $resource): ?string
    {
        return $resource->data->getString(
            'external.label',
            $resource->data->getString(
                'base.title',
                $resource->data->getString('name'),
            ),
        );
    }

    protected function getAccessibilityLabel(SiteKitResource $resource): ?string
    {
        return $resource->data->has('external.accessibilityLabel')
            ? $resource->data->getString('external.accessibilityLabel')
            : null;
    }

    protected function getDescription(SiteKitResource $resource): ?string
    {
        return $resource->data->has('external.description')
            ? $resource->data->getString('external.description')
            : null;
    }

    protected function opensNewWindow(SiteKitResource $resource): bool
    {
        return $resource->data->getBool('external.newWindow', false);
    }

    protected function isExternal(SiteKitResource $resource): bool
    {
        return $resource->data->getBool('external.external', false);
    }

    protected function isMedia(SiteKitResource $resource): bool
    {
        if ($resource->objectType === 'media') {
            return true;
        }
        if ($resource->objectType === 'embedded-media') {
            return true;
        }
        return false;
    }
}
