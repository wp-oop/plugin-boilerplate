<?php

declare(strict_types=1);

namespace Me\Plugin\Test\Func;

use Dhii\Container\DelegatingContainer;
use Dhii\Modular\Module\ModuleInterface;
use Interop\Container\ServiceProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Me\Plugin\ModularModule as Subject;
use Psr\Container\ContainerInterface;

class ModularModuleTest extends TestCase
{
    public function testServicesActionsWorkCorrectly()
    {
        {
            $service1Name = uniqid('service1Name');
            $service1Value = uniqid('service1Value');
            $extension1Value = uniqid('extension1Value');
            $service2Name = uniqid('service1Name');
            $service2Value = uniqid('service2Value');
            $output1 = uniqid('output1');
            $output2 = uniqid('output2');
            $output3 = uniqid('output3');

            $module1 = $this->createModule(
                [
                    $service1Name => function (): string {
                        return uniqid('service1-1');
                    }
                ],
                [],
                function () use ($output1): void {
                    echo $output1;
                }
            );
            $module2 = $this->createModule(
                [
                    $service1Name => function () use ($service1Value): string {
                        return $service1Value;
                    }
                ],
                [],
                function () use ($output2): void {
                    echo $output2;
                }
            );
            $module3 = $this->createModule(
                [
                    $service2Name => function () use ($service2Value): string {
                        return $service2Value;
                    }
                ],
                [
                    $service1Name => function (ContainerInterface $c, string $prev) use ($extension1Value): string {
                        return $prev . $extension1Value;
                    },
                ],
                function () use ($output3): void {
                    echo $output3;
                }
            );

            $subject = $this->createSubject([$module1, $module2, $module3]);
        }

        {
            $this->expectOutputString(implode('', [$output1, $output2, $output3]));
        }

        {
            $provider = $subject->setup();
            $container = $this->createContainer($provider);
            $subject->run($container);

            $this->assertEquals($service1Value . $extension1Value, $container->get($service1Name));
            $this->assertEquals($service2Value, $container->get($service2Name));
        }
    }

    /**
     * @param iterable|ModuleInterface[] $modules The modules to load.
     *
     * @return Subject|MockObject
     */
    protected function createSubject(iterable $modules): Subject
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setConstructorArgs([$modules])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        return $mock;
    }

    /**
     * @return ContainerInterface|MockObject
     */
    protected function createContainer(ServiceProviderInterface $provider): ContainerInterface
    {
        $mock = $this->getMockBuilder(DelegatingContainer::class)
            ->enableProxyingToOriginalMethods()
            ->setConstructorArgs([$provider])
            ->getMock();

        return $mock;
    }

    /**
     * @param array    $factories
     * @param array    $extensions
     * @param callable $action
     *
     * @return ModuleInterface|MockObject
     */
    protected function createModule(array $factories, array $extensions, callable $action): ModuleInterface
    {
        $module = $this->getMockBuilder(ModuleInterface::class)
            ->setMethods(['setup', 'run'])
            ->getMock();
        $provider = $this->getMockBuilder(ServiceProviderInterface::class)
            ->setMethods(['getFactories', 'getExtensions'])
            ->getMock();

        $provider->method('getFactories')
            ->will($this->returnValue($factories));
        $provider->method('getExtensions')
            ->will($this->returnValue($extensions));

        $module->method('setup')
            ->will($this->returnValue($provider));
        $module->method('run')
            ->will($this->returnCallback($action));

        return $module;
    }
}
