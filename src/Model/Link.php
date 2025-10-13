<?php

namespace Atoolo\Resource\Model;

final class Link
{
    public function __construct(
        public readonly string $url,
        public readonly ?string $label = null,
        public readonly ?string $accessibilityLabel = null,
        public readonly ?string $description = null,
        public readonly ?string $rel = null,
        public readonly ?string $target = null,
        public readonly bool $isExternal = false,
    ) {}
}
