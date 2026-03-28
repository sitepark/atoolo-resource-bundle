<?php

namespace Atoolo\Resource\Model;

final class TaxonomyTerm
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $url = null,
    ) {}
}
