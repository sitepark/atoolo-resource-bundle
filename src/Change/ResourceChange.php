<?php

declare(strict_types=1);

namespace Atoolo\Resource\Change;

/**
 * A resource the CMS has created or changed.
 *
 * The path is the one of the published file, relative to the directory of
 * its publication type - a translation is addressed by its own file, e.g.
 * `/dir/file.php.translations/en_US.php`.
 *
 * The publication type is the one of the CMS, e.g. `OBJECT` or `CONFIG`; a
 * CMS that does not report it reports resources only. As the path lacks the
 * directory of the publication type, only the type tells the file of a
 * configuration, below `configs`, from the one of a resource.
 */
final class ResourceChange
{
    public const PUBLICATION_TYPE_OBJECT = 'OBJECT';

    public const PUBLICATION_TYPE_MEDIA = 'MEDIA';

    public const PUBLICATION_TYPE_EMBEDDED_MEDIA = 'EMBEDDED_MEDIA';

    public const PUBLICATION_TYPE_CONFIG = 'CONFIG';

    private const RESOURCE_TYPES = [
        self::PUBLICATION_TYPE_OBJECT,
        self::PUBLICATION_TYPE_MEDIA,
        self::PUBLICATION_TYPE_EMBEDDED_MEDIA,
    ];

    public function __construct(
        public readonly string $id,
        public readonly string $path,
        public readonly ?string $publicationType = null,
    ) {}

    /**
     * Whether the file is one of the resource directory: an object or a
     * medium, or a file of unknown publication type.
     */
    public function isResource(): bool
    {
        return $this->publicationType === null
            || in_array($this->publicationType, self::RESOURCE_TYPES, true);
    }
}
