<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * @codeCoverageIgnore
 */
class Manifest
{
    /**
     * @param int $home
     * @param array<string, int> $errors
     */
    public function __construct(
        public readonly int $home,
        public readonly array $errors,
    ) {}
}
