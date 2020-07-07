<?php

declare(strict_types=1);

namespace Me\Plugin;

use Dhii\Container\CompositeCachingServiceProvider;
use Dhii\Modular\Module\ModuleInterface;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

/**
 * A module that loads other modules.
 */
class ModularModule implements ModuleInterface
{
    /**
     * @var ModuleInterface[]|iterable
     */
    protected $modules;

    /**
     * @param iterable|ModuleInterface[] $modules The modules to load.
     */
    public function __construct(iterable $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @inheritDoc
     */
    public function setup(): ServiceProviderInterface
    {
        $providers = [];
        foreach ($this->modules as $module) {
            assert($module instanceof ModuleInterface);
            $providers[] = $module->setup();
        }

        return new CompositeCachingServiceProvider($providers);
    }

    /**
     * @inheritDoc
     */
    public function run(ContainerInterface $c)
    {
        foreach ($this->modules as $module) {
            assert($module instanceof ModuleInterface);
            $module->run($c);
        }
    }
}
