<?php

declare(strict_types=1);

namespace Atoolo\Resource\Service;

use Atoolo\Resource\AbstractResource;
use Atoolo\Resource\Capabilities\MetadataCapability;
use Atoolo\Resource\ResourceCapabilityFactory;

class ResourceCapabilityFactoryExampleService
{
    public function __construct(
        private readonly ResourceCapabilityFactory $resourceCapabilityFactory,
    ) {}

    public function printMetaTitleOfAnyResource(
        AbstractResource $resource,
    ): void {
        $metadata = $this->resourceCapabilityFactory->create(
            $resource,
            MetadataCapability::class,
        );

        if ($metadata === null) {
            echo "resource has no metadata";
            return;
        }

        echo $metadata->getTitle();
    }
}
