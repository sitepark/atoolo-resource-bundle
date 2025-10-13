<?php

declare(strict_types=1);

namespace Atoolo\Resource\ResourceFeature;

use Atoolo\Resource\Model\TaxonomyTerm;

/**
 * Provides access to taxonomies associated with a resource,
 * such as categories, tags, or keywords.
 */
final class TaxonomyFeature implements ResourceFeature
{
    /**
     * @param array<string, TaxonomyTerm[]> $termsByTaxonomy An array where keys are taxonomy names (e.g., 'categories')
     * and values are arrays of Term objects.
     */
    public function __construct(
        public readonly array $termsByTaxonomy = [],
    ) {}
}
