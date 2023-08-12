<?php

declare(strict_types=1);

namespace Me\Plugin\Test;

use Psr\Container\ContainerInterface;
use Dhii\Modular\Module\Exception\ModuleExceptionInterface;
use Me\Plugin\MainModule;
use RuntimeException;

/**
 * Base class for tests of the application layer,
 * e.g. a combination of application modules.
 */
class AbstractApplicationTestCase extends AbstractModularTestCase
{
    /**
     * Retrieve a new bootstrapped application container.
     *
     * @param array<string, callable(ContainerInterface): mixed> $factories Overriding factories.
     * @param array<string, callable(ContainerInterface, mixed): mixed> $extensions Additional extensions.
     *
     * @throws ModuleExceptionInterface If problem bootstrapping a particular module.
     * @throws RuntimeException If problem bootstraping.
     */
    protected function bootstrapApplication(array $factories = [], array $extensions = []): ContainerInterface
    {
        $baseDir = BASE_DIR;
        $basePath = BASE_PATH;
        $mainModule = new MainModule($basePath, $baseDir);
        $appModules = array_merge((require "$baseDir/src/modules.php")($basePath, $baseDir), [$mainModule]);
        $appContainer = $this->bootstrapModules($appModules, $factories, $extensions);

        return $appContainer;
    }
}
