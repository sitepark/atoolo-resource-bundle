<?php

declare(strict_types=1);

namespace Atoolo\Resource\Capabilities;

use Atoolo\Resource\AbstractResource;
use Atoolo\Resource\DataBag;
use Atoolo\Resource\Resource;

class SiteKitMetadataCapability implements MetadataCapability
{
    private DataBag $data;

    public function __construct(AbstractResource $resource)
    {
        if (!($resource instanceof Resource)) {
            throw new \RuntimeException('resource is not of type ' . Resource::class);
        }
        $this->data = $resource->data;
    }

    public function getTitle(): string
    {
        return $this->data->getString('metadata.headline');
    }

    public function getDescription(): string
    {
        return $this->data->getString('metadata.description');
    }

    public function getKeywords(): array
    {
        /** @var string[] $keywords */
        $keywords = $this->data->getArray('metadata.keywords');
        return $keywords;
    }

    public function getAuthor(): ?string
    {
        return null; // TODO
    }

    public function getPublishDate(): ?\DateTimeInterface
    {
        return null; // TODO
    }


    public function getModifiedDate(): ?\DateTimeInterface
    {
        return null; // TODO
    }

    public function getCanonicalUrl(): ?string
    {
        return null; // TODO
    }


    public function isIndexable(): bool
    {
        return true; // TODO
    }

    public function isFollowable(): bool
    {
        return true; // TODO
    }

    public function getSocialShareTitle(): string
    {
        return $this->getTitle(); // TODO
    }

    public function getSocialShareDescription(): string
    {
        return $this->getDescription(); // TODO
    }

    public function getSocialShareImageUrl(): ?string
    {
        return null; // TODO
    }
}
