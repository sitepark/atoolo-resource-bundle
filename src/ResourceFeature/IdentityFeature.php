<?php

namespace Atoolo\Resource\ResourceFeature;

/**
 * Basic data about the identity of a feature
 */
final class IdentityFeature implements ResourceFeature
{
    /**
     * @param string $id Stable unique identifier for this resource. Must not change even if URL changes.
     * @param string $url Absolute URL to access this resource
     */
    public function __construct(
        public readonly string $id,
        public readonly string $url,
    ) {}
}
