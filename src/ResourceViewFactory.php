<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * Factory for creating ResourceViews using registered contributors.
 */
final class ResourceViewFactory
{
    /**
     * @param iterable<ResourceViewContributor> $contributors
     */
    public function __construct(private iterable $contributors) {}

    /**
     * Creates a ResourceView for a given resource.
     */
    public function createView(Resource $resource): ResourceView
    {
        $builder = new ResourceViewBuilder();
        foreach ($this->contributors as $contributor) {
            if ($contributor->supports($resource)) {
                $contributor->contribute($resource, $builder);
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
     * @param Resource[] $resources
     * @return array<string,ResourceView> keyed by resource ID
     */
    public function createViews(array $resources): array
    {
        $builders = [];
        foreach ($resources as $r) {
            $builders[$r->id] = new ResourceViewBuilder();
        }
        foreach ($this->contributors as $contributor) {
            $supportedResources = [];
            $buildersForSupportedResources = [];
            foreach ($resources as $r) {
                if ($contributor->supports($r)) {
                    $supportedResources[] = $r;
                    $buildersForSupportedResources[$r->id] = $builders[$r->id];
                }
            }
            if (empty($supportedResources)) {
                continue;
            }
            if ($contributor instanceof ResourceViewBatchContributor) {
                $contributor->contributeBatch($supportedResources, $buildersForSupportedResources);
            } else {
                foreach ($supportedResources as $r) {
                    $contributor->contribute($r, $buildersForSupportedResources[$r->id]);
                }
            }
        }
        return array_map(fn($b) => $b->build(), $builders);
    }
}
