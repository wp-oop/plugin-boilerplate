<?php

declare(strict_types=1);

use Dhii\Container\CachingContainer;
use Dhii\Container\CompositeCachingServiceProvider;
use Dhii\Container\CompositeContainer;
use Dhii\Container\DelegatingContainer;
use Dhii\Container\ProxyContainer;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

return
    /**
     * Bootstraps a container with a list of providers.
     *
     * @param iterable<ServiceProviderInterface> $providers A list of providers to bootstrap.
     * @param array<ContainerInterface> $additionalContainers A list of any containers that are added to the
     *                                                        application, capable of overriding any service.
     */
    function (
        iterable $providers,
        array $additionalContainers = []
    ): ContainerInterface {
        // Combine module service providers
        $compositeProvider = new CompositeCachingServiceProvider($providers);
        // See below
        $proxyContainer = new ProxyContainer();
        // Exposes services of all modules
        $container = new DelegatingContainer($compositeProvider, $proxyContainer);
        // Exposes services of the whole application
        $appContainer = new CachingContainer(
            new CompositeContainer(
                array_merge(
                    $additionalContainers,
                    array($container)
                )
            )
        );
        /*
         * This is necessary to go around the chicken-egg problem,
         * and allow the modules' container services to access services
         * from the application container.
         */
        $proxyContainer->setInnerContainer($appContainer);

        return $appContainer;
    };
