<?php

declare(strict_types=1);

use Atoolo\Resource\Test\TestResourceFactory;

return TestResourceFactory::create([
    'url' => '/a.php',
    'id' => 'a',
    'name' => 'a',
    'locale' => 'de_DE',
    'home' => true,
]);
