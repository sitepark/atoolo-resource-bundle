<?php

namespace Atoolo\Resource\Model\OpenGraph;

final class OpenGraphAudio
{
    public function __construct(
        public readonly string $url,
        public readonly ?string $secure_url = null,
        public readonly ?string $type = null,
    ) {}
}
