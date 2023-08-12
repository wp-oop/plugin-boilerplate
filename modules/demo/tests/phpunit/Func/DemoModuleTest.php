<?php

declare(strict_types=1);


namespace Me\Plugin\Demo\Test\Func;

use Dhii\Services\Factories\Value;
use Me\Plugin\Test\AbstractModularTestCase;
use Me\Plugin\Demo\DemoModule as Subject;

class DemoModuleTest extends AbstractModularTestCase
{
    public function testBootstrap()
    {
        $pluginTitle = uniqid('My Plugin');
        $container = $this->bootstrapModules([new Subject()], [
            'me/plugin/demo/plugin_title' => new Value($pluginTitle),
        ]);

        $noticeText = $container->get('me/plugin/demo/notice_text');
        $this->assertStringContainsString($pluginTitle, $noticeText, 'Notice text doesn\'t contain plugin name');
    }
}
