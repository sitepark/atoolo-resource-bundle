<?php

declare(strict_types=1);

use Atoolo\Resource\Test\TestResourceFactory;

return TestResourceFactory::create([
    'url' => '/index.php',
    'id' => 'root',
    'name' => 'root',
    'locale' => 'de_DE',
    'home' => true,
]);