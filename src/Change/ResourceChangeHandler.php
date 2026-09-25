<?php

declare(strict_types=1);

namespace Atoolo\Resource\Change;

/**
 * Reacts to resources the CMS has created, changed or removed.
 *
 * Handlers are called asynchronously, some seconds after the CMS sent the
 * notification. If a handler throws, the notification is handed to all
 * handlers again later, so a handler has to be idempotent. A handler that
 * cannot handle the changes yet throws a
 * {@see ResourceChangeDeferredException}; the changes are then repeated
 * without counting as a failed attempt.
 *
 * An autoconfigured service implementing this interface is tagged
 * {@see TAG} by the bundle; a service that is not autoconfigured needs the
 * tag itself.
 */
interface ResourceChangeHandler
{
    public const TAG = 'atoolo_resource.resource_change_handler';

    public function handle(ResourceChanges $changes): void;
}
