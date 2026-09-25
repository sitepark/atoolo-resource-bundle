<?php

declare(strict_types=1);

namespace Atoolo\Resource\Change;

use RuntimeException;

/**
 * Thrown by a {@see ResourceChangeHandler} that cannot handle the changes
 * yet, e.g. because a full index run is in progress. Unlike a failure it
 * does not count as an attempt: the message is repeated as long as it
 * takes, see {@see ResourceChangeMessageHandler}.
 */
class ResourceChangeDeferredException extends RuntimeException {}
