<?php

declare(strict_types=1);

namespace Atoolo\Resource;

/**
 * In the Atoolo context, sitekit resources are aggregated data from
 * IES (Sitepark's content management system).
 */
class Resource extends AbstractResource
{
    public function __construct(
        string $location,
        string $id,
        string $name,
        public readonly string $objectType,
        ResourceLanguage $lang,
        public readonly DataBag $data,
    ) {
        parent::__construct($location, $id, $name, $lang);
    }
}
