<?php

namespace Atoolo\Resource\Model\OpenGraph;

final class OpenGraphImage
{
    public function __construct(
        public readonly string $url,
        public readonly ?string $secure_url = null,
        public readonly ?string $type = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?string $alt = null,
    ) {}
}
