<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * Contributes features to multiple ResourceViews at once.
 *
 * This allows batch contributors to optimize expensive operations
 * (e.g., database or API lookups) by handling multiple resources together.
 */
interface ResourceViewBatchContributor
{
    /**
     * @param Resource[] $resources
     * @param array<string,ResourceViewBuilder> $builders keyed by resource ID.
     */
    public function contributeBatch(array $resources, array $builders): void;
}
