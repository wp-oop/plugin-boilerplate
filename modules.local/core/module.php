<?php

use Dhii\Container\ServiceProvider;
use Dhii\Modular\Module\ModuleInterface;
use Me\Plugin\Core\Module;

return function (string $rootDir, string $mainFile): ModuleInterface {
    $rootDir = dirname($mainFile);
    $moduleDir = dirname(__FILE__);
    $moduleIncDir = "$moduleDir/inc";

    /** @psalm-suppress UnresolvableInclude */
    $factories = (require "$moduleIncDir/factories.php")($rootDir, $mainFile);
    /** @psalm-suppress UnresolvableInclude */
    $extensions = (require "$moduleIncDir/extensions.php")($rootDir, $mainFile);
    /**
     * @var array<array-key, callable> $factories
     * @var array<array-key, callable> $extensions
     */

    $provider = new ServiceProvider($factories, $extensions);
    $module = new Module($provider);

    return $module;
};
