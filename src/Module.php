<?php

declare(strict_types=1);

namespace Me\Plugin;

use Dhii\Modular\Module\ModuleInterface;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

/**
 * A generic module.
 */
class Module implements ModuleInterface
{
    /** @var ServiceProviderInterface */
    protected $serviceProvider;

    public function __construct(ServiceProviderInterface $serviceProvider)
    {
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * @inheritDoc
     */
    public function setup(): ServiceProviderInterface
    {
        return $this->serviceProvider;
    }

    /**
     * @inheritDoc
     */
    public function run(ContainerInterface $c): void
    {
    }
}
