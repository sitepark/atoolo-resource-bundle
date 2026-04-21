<?php

declare(strict_types=1);

use Atoolo\Resource\Resource;

return Resource::create([
    'url' => '/primaryParentWithInvalidData.php',
    'id' => 'primaryParentWithInvalidData',
    'name' => 'primaryParentWithInvalidData',
    'locale' => 'en_US',
    'base' => [
        'trees' => [
            'category' => [
                'parents' => [
                    'a' => 'invalid',
                ],
            ],
        ],
    ],
]);
