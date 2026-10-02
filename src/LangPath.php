<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * @codeCoverageIgnore
 */
class LangPath
{
    public function __construct(
        public readonly ?string $lang,
        public readonly ?string $locale,
        public readonly string $path,
    ) {}
}
