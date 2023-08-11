<?php

declare(strict_types=1);

use Me\Plugin\Demo\DemoModule;

return function (string $rootDir, string $mainFile): iterable {
    $modules = [
        new DemoModule(),
    ];

    return $modules;
};
