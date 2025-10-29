<?php

declare(strict_types=1);

namespace Atoolo\Resource;

final class ResourceViewBuilderBagEntry
{
    public readonly \Closure $factory;

    public function __construct(
        public readonly int $priority,
        callable $factory,
    ) {
        $this->factory = \Closure::fromCallable($factory);
    }
}
