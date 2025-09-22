<?php

declare(strict_types=1);

namespace Atoolo\Resource\Exception;

use Atoolo\Resource\ResourceLocation;
use Atoolo\Resource\ResourceView;

/**
 * This exception is used when a resource feature is requested that
 * does not exist in a given resource view.
 */
class MissingFeatureException extends \LogicException
{
    public function __construct(
        public readonly string $feature,
        public readonly ResourceView $resourceView,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            "Feature $feature not available.",
            $code,
            $previous,
        );
    }
}
