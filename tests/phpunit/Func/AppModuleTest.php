<?php

declare(strict_types=1);

namespace Me\Plugin\Test\Func;

use Me\Plugin\Test\PluginFunctionMocks;
use Psr\Container\ContainerInterface;
use Me\Plugin\Test\AbstractApplicationTestCase;

class AppModuleTest extends AbstractApplicationTestCase
{
    use PluginFunctionMocks;

    public function testInitializationAndExtension()
    {
        {
            $this->mockPluginFunctions();
            $serviceName = uniqid('serviceName');
            $factoryValue = uniqid('serviceValue');
            $extensionValue = uniqid('extensionValue');
            $valueSeparator = '/';

            $container = $this->bootstrapApplication(
                [
                    'me/plugin/main_file_path' => function () {
                        return BASE_PATH;
                    },
                    'me/plugin/basedir' => function () {
                        return BASE_DIR;
                    },
                    'wp/core/abspath' => function () {
                        return ABSPATH;
                    },

                    $serviceName => function () use ($factoryValue): string {
                        return $factoryValue;
                    },
                ],
                [
                    $serviceName => function (ContainerInterface $c, string $prev) use ($extensionValue, $valueSeparator): string {
                        return "{$prev}{$valueSeparator}{$extensionValue}";
                    },
                ]
            );
        }

        {
            $serviceValue = $container->get($serviceName);
            $this->assertEquals("{$factoryValue}{$valueSeparator}{$extensionValue}", $serviceValue);
        }
    }
}
