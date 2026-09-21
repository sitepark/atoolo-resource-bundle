<?php

declare(strict_types=1);

use Atoolo\Resource\Resource;

return Resource::create([
    'url' => '/dir/b.php',
    'id' => 'a',
    'name' => 'a',
    'locale' => 'en_US',
    'base' => [
        'trees' => [
            'navigation' => [
                'parents' => [
                    'a' => [
                        'url' => '/dir/a.php',
                        'id' => 'a',
                    ],
                ],
            ],
        ],
    ],
]);
