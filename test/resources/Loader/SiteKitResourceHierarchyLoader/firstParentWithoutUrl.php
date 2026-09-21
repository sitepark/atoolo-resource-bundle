<?php

declare(strict_types=1);

use Atoolo\Resource\Resource;

return Resource::create([
    'url' => '/firstParentWithoutUrl.php',
    'id' => 'firstParentWithoutUrl',
    'name' => 'firstParentWithoutUrl',
    'locale' => 'en_US',
    'base' => [
        'trees' => [
            'category' => [
                'parents' => [
                    'a' => [
                    ],
                ],
            ],
        ],
    ],
]);
