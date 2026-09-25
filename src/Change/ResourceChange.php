<?php

declare(strict_types=1);

namespace Atoolo\Resource\Change;

/**
 * A resource the CMS has created or changed.
 *
 * The path is the one of the published file, relative to the resource
 * directory - a translation is addressed by its own file, e.g.
 * `/dir/file.php.translations/en_US.php`.
 */
final class ResourceChange
{
    public function __construct(
        public readonly string $id,
        public readonly string $path,
    ) {}
}
