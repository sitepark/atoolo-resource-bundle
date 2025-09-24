<?php

declare(strict_types=1);

namespace Atoolo\Resource;

use Atoolo\Resource\ResourceFeature;

final class ResourceViewBuilder
{
    /**
     * @var array<class-string<ResourceFeature>, ResourceViewBuilderBagEntry>
     */
    private array $bag = [];

    /**
     * @template T of ResourceFeature
     * @param class-string<T> $feature
     * @param ResourceFeature|(callable():T) $value
     */
    public function add(string $feature, ResourceFeature|callable $value, int $priority = 0): void
    {
        $cur = $this->bag[$feature] ?? null;
        $factory = $value instanceof ResourceFeature
            ? fn() => $value
            : $value;
        if ($cur === null || $priority > $cur->priority) {
            $this->bag[$feature] = new ResourceViewBuilderBagEntry($priority, $factory);
        }
    }

    public function build(): ResourceView
    {
        $factories = [];
        foreach ($this->bag as $class => $entry) {
            $factories[$class] = $entry->factory;
        }
        return new ResourceView($factories);
    }
}
