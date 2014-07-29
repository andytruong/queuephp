<?php

$locations[] = __DIR__ . "/../vendor/autoload.php";
$locations[] = __DIR__ . "/../../../autoload.php";

foreach ($locations as $location) {
    if (is_file($location)) {
        $loader = require $location;
        $loader->addPsr4('AndyTruong\\QueuePHP\\', __DIR__ . '/../src');
        $loader->addPsr4('AndyTruong\\QueuePHP\\Driver\\', __DIR__ . '/../drivers');
        $loader->addPsr4('AndyTruong\\QueuePHP\\TestCases\\', __DIR__ . '/queuephp');
        $loader->addPsr4('AndyTruong\\QueuePHP\\TestCases\\Drivers\\', __DIR__ . '/drivers');
        $loader->addPsr4('AndyTruong\\QueuePHP\\Fixtures\\', __DIR__ . '/fixtures');
        break;
    }
}
