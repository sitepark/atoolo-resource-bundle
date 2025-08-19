<?php

declare(strict_types=1);

namespace Atoolo\Resource;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class ResourceCapabilityFactory implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param array<string,array<string,string>> $capabilityImplementationMap
     */
    public function __construct(
        private readonly array $capabilityImplementationMap,
    ) {}

    /**
     * @template T
     * @param class-string<T> $capabilityInterface
     * @return T|null
     */
    public function create(AbstractResource $resource, string $capabilityInterface): mixed
    {
        $resourceClass = get_class($resource);

        if (!isset($this->capabilityImplementationMap[$resourceClass])) {
            $this->logger?->warning(
                'no resource config found for resource class',
                [
                    'resource_class' => $resourceClass,
                ],
            );
            return null;
        }
        $capabilityImplementation = $this->capabilityImplementationMap[$resourceClass][$capabilityInterface] ?? null;
        if ($capabilityImplementation === null) {
            return null;
        }
        /** @var T $capabilityInstance */
        $capabilityInstance = new $capabilityImplementation($resource);
        return $capabilityInstance;
    }
}
