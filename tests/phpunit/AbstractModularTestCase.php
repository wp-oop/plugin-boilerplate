<?php

declare(strict_types=1);

namespace Me\Plugin\Test;

use Dhii\Container\ServiceProvider;
use Dhii\Modular\Module\Exception\ModuleExceptionInterface;
use Dhii\Modular\Module\ModuleInterface;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

/**
 * Base class for module tests.
 */
class AbstractModularTestCase extends AbstractTestCase
{
    /**
     * Retrieve a new container with the specified service providers bootstrapped.
     *
     * @param iterable<ServiceProviderInterface> $serviceProviders The list of service providers to bootstrap.
     *
     * @return ContainerInterface The new container.
     * @throws ModuleExceptionInterface If problem bootstrapping a particular module.
     * @throws RuntimeException If problem bootstraping.
     */
    protected function bootstrapProviders(iterable $serviceProviders = []): ContainerInterface
    {
        $baseDir = BASE_DIR;
        $bootstrap = require "$baseDir/src/bootstrap.php";
        $modulesContainer = $bootstrap($serviceProviders);

        return $modulesContainer;
    }

    /**
     * Retrieve a new container with the specified modules bootstrapped.
     *
     * @param iterable<ModuleInterface> $modules The list of modules to bootstrap.
     * @param array<string, callable(ContainerInterface): mixed> $factories Overriding factories.
     * @param array<string, callable(ContainerInterface, mixed): mixed> $extensions Additional extensions.
     *
     * @return ContainerInterface The new container.
     * @throws ModuleExceptionInterface If problem bootstrapping a particular module.
     * @throws RuntimeException If problem bootstraping.
     */
    protected function bootstrapModules(
        iterable $modules = [],
        array $factories = [],
        array $extensions = []
    ): ContainerInterface {
        if (!is_array($modules)) {
            $modules = iterator_to_array($modules);
        }

        $extraProvider = new ServiceProvider($factories, $extensions);
        $providers = array_map(function(ModuleInterface $module) { return $module->setup(); }, $modules);
        $allProviders = array_merge($providers, [$extraProvider]);
        $container = $this->bootstrapProviders($allProviders);

        // Run each module
        array_walk($modules, function(ModuleInterface $module) use ($container) { $module->run($container); });

        return $container;
    }
}
