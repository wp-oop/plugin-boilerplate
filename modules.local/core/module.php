<?php

use Dhii\Container\ServiceProvider;
use Dhii\Modular\Module\ModuleInterface;
use Me\Plugin\Core\Module;

return function (string $rootDir, string $mainFile): ModuleInterface {
    $rootDir = dirname($mainFile);
    $moduleDir = dirname(__FILE__);
    $moduleIncDir = "$moduleDir/inc";
    $factories = (require "$moduleIncDir/factories.php")($rootDir, $mainFile);
    $extensions = (require "$moduleIncDir/extensions.php")($rootDir, $mainFile);
    $provider = new ServiceProvider($factories, $extensions);
    $module = new Module($provider);

    return $module;
};
