<?php

declare(strict_types=1);

namespace Atoolo\Resource\Change;

/**
 * The changes the CMS reported for a channel, on their way to the worker
 * of that channel.
 */
final class ResourceChangeMessage
{
    public function __construct(
        public readonly ResourceChanges $changes,
    ) {}
}
