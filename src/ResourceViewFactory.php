<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * Factory for creating ResourceViews using registered contributors.
 */
final class ResourceViewFactory
{
    /**
     * @param iterable<ResourceViewContributor> $resolvers
     */
    public function __construct(private iterable $resolvers) {}

    /**
     * Creates a ResourceView for a given resource.
     */
    public function create(AbstractResource $resource): ResourceView
    {
        $builder = new ResourceViewBuilder();
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($resource)) {
                $resolver->contribute($resource, $builder);
            }
        }
        return $builder->build();
    }

    /**
     * Builds ResourceViews for multiple resources in one pass.
     *
     * Contributors that implement ResourceViewBatchContributor
     * can optimize batch creation
     *
     * @param AbstractResource[] $resources
     * @return array<string,ResourceView> keyed by resource ID
     */
    public function createBatch(array $resources): array
    {
        $builders = [];
        foreach ($resources as $r) {
            $builders[$r->id] = new ResourceViewBuilder();
        }
        foreach ($this->resolvers as $resolver) {
            $supportedResources = [];
            $buildersForSupportedResources = [];
            foreach ($resources as $r) {
                if ($resolver->supports($r)) {
                    $supportedResources[] = $r;
                    $buildersForSupportedResources[$r->id] = $builders[$r->id];
                }
            }
            if (empty($supportedResources)) {
                continue;
            }
            if ($resolver instanceof ResourceViewBatchContributor) {
                $resolver->batchContribute($supportedResources, $buildersForSupportedResources);
            } else {
                foreach ($supportedResources as $r) {
                    $resolver->contribute($r, $buildersForSupportedResources[$r->id]);
                }
            }
        }
        return array_map(fn($b) => $b->build(), $builders);
    }
}
