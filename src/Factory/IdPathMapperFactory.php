<?php

declare(strict_types=1);

namespace Atoolo\Resource\Factory;

use Atoolo\Resource\ResourceChannel;
use Atoolo\Resource\Service\FixedDecimalGroupingIdPathMapper;
use Atoolo\Resource\Service\IdPathMapper;
use Atoolo\Resource\Service\NoopIdPathMapper;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsAlias(id: 'atoolo_resource.resource_id_path_mapper_factory')]
class IdPathMapperFactory
{
    public function __construct(
        #[Autowire(service: 'atoolo_resource.resource_channel')]
        private readonly ResourceChannel $resourceChannel,
    ) {}

    public function create(): ?IdPathMapper
    {
        if ($this->resourceChannel->attributes->getString('resourcePathType') !== 'id') {
            return new NoopIdPathMapper();
        }
        return new FixedDecimalGroupingIdPathMapper(2, 3);
    }
}
