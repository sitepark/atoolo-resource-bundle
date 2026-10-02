<?php

declare(strict_types=1);

use Atoolo\Resource\Resource;

return Resource::create([
    'url' => '/withRecursionA.php',
    'id' => 'withRecursionA',
    'name' => 'withRecursionA',
    'locale' => 'en_US',
    'base' => [
        'trees' => [
            'category' => [
                'parents' => [
                    'withRecursionB' => [
                        'url' => '/withRecursionB.php',
                        'id' => 'withRecursionB',
                    ],
                ],
            ],
        ],
    ],
]);
