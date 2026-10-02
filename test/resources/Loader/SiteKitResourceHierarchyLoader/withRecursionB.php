<?php

declare(strict_types=1);

use Atoolo\Resource\Resource;

return Resource::create([
    'url' => '/withRecursionB.php',
    'id' => 'withRecursionB',
    'name' => 'withRecursionB',
    'locale' => 'en_US',
    'base' => [
        'trees' => [
            'category' => [
                'parents' => [
                    'withRecursionA' => [
                        'url' => '/withRecursionA.php',
                        'id' => 'withRecursionA',
                    ],
                ],
            ],
        ],
    ],
]);
