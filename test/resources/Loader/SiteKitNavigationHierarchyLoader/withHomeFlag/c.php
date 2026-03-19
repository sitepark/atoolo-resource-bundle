<?php

declare(strict_types=1);

use Atoolo\Resource\Test\TestResourceFactory;

return TestResourceFactory::create([
    'url' => '/c.php',
    'id' => 'c',
    'name' => 'c',
    'locale' => 'en_US',
    'base' => [
        'trees' => [
            'navigation' => [
                'parents' => [
                    'b' => [
                        'isPrimary' => true,
                        'url' => '/b.php',
                        'id' => 'b',
                    ],
                    'a' => [
                        'url' => '/a.php',
                        'id' => 'a',
                    ],
                ],
            ],
        ],
    ],
]);
