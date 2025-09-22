<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * Contributes features to a ResourceView for supported resources.
 */
interface ResourceViewContributor
{
    /**
     * Checks whether this contributor supports a given resource.
     */
    public function supports(AbstractResource $r): bool;

    /**
     * Adds features to the given ResourceViewBuilder for a supported resource.
     */
    public function contribute(AbstractResource $r, ResourceViewBuilder $b): void;
}
