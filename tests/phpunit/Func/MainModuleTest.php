<?php

declare(strict_types=1);

namespace Me\Plugin\Test\Func;

use Dhii\Services\Factories\Value;
use Me\Plugin\Plugin;
use Me\Plugin\Test\PluginFunctionMocks;
use Psr\Container\ContainerInterface;
use Me\Plugin\MainModule as Subject;
use Me\Plugin\Test\AbstractModularTestCase;

use function Brain\Monkey\Functions\when;

class MainModuleTest extends AbstractModularTestCase
{
    use PluginFunctionMocks;

    public function testMainModuleLoads()
    {
        $this->mockPluginFunctions();
        $appContainer = $this->bootstrapModules([new Subject(BASE_PATH, BASE_DIR)],[
            'me/plugin/main_file_path' => new Value(BASE_PATH),
        ]);
        $this->assertInstanceOf(ContainerInterface::class, $appContainer);
        $this->assertInstanceOf(Plugin::class, $appContainer->get('me/plugin/plugin'));
        $this->assertFalse($appContainer->has(uniqid('non-existing-service')));
    }
}
