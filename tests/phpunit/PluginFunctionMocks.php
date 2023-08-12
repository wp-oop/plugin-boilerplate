<?php

namespace Me\Plugin\Test;

use function Brain\Monkey\Functions\when;

/**
 * Common mocks for tests that are plugin-specific.
 */
trait PluginFunctionMocks
{
    /**
     * Mocks functions specific to this plugin.
     */
    protected function mockPluginFunctions(): void
    {
        when('get_plugin_data')
            ->justReturn([
                'Name' => uniqid('My WordPress Plugin '),
                'Description' => 'Test data for my WordPress plugin',
            ]);

        when('plugin_basename')
            ->justReturn('plugin/plugin.php');
    }
}
