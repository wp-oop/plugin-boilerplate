<?php

use Dhii\Container\CachingContainer;
use Dhii\Container\CompositeContainer;
use Dhii\Container\DelegatingContainer;
use Dhii\Container\ProxyContainer;
use Dhii\Modular\Module\ModuleInterface;
use Psr\Container\ContainerInterface;

return function (string $rootDir, string $mainFile): ContainerInterface {
    $module = (require "$rootDir/module.php")($rootDir, $mainFile);
    assert($module instanceof ModuleInterface);
    $provider = $module->setup();

    $proxyContainer = new ProxyContainer();
    $container = new DelegatingContainer($provider, $proxyContainer);
    $appContainer = new CachingContainer(new CompositeContainer([
        $container
    ]));
    $proxyContainer->setInnerContainer($appContainer);

    $module->run($appContainer);

    return $appContainer;
};
