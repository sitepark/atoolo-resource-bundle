<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * High-level entry point for retrieving ResourceViews with caching.
 *
 * Ensures that once a ResourceView has been created for a resource,
 * it is reused for subsequent requests.
 */
final class ResourceViewManager
{
    public function __construct(private ResourceViewFactory $factory) {}

    /**
     * @var array<string, ResourceView>
     */
    private array $cache = [];

    /**
     * Retrieves the ResourceView for a given resource, creating and caching if needed.
     */
    public function forResource(AbstractResource $resource): ResourceView
    {
        return $this->cache[$resource->id] ??= $this->factory->createView($resource);
    }

    /**
     * Retrieves ResourceViews for multiple resources with caching.
     *
     * Already-cached views are reused, missing ones are created in batch.
     *
     * @param AbstractResource[] $resources
     * @return array<string,ResourceView> keyed by resource ID
     */
    public function forResources(array $resources): array
    {
        $cachedViews = [];
        $uncachedResources = array_filter(
            $resources,
            function (AbstractResource $resource) use (&$cachedViews) {
                if (isset($this->cache[$resource->id])) {
                    $cachedViews[$resource->id] = $this->cache[$resource->id];
                    return false;
                }
                return true;
            },
        );
        $newViews = [];
        if (!empty($uncachedResources)) {
            $newViews = $this->factory->createViews($uncachedResources);
            foreach ($newViews as $id => $view) {
                $this->cache[$id] = $view;
            }
        }
        return $cachedViews + $newViews;
    }
}
