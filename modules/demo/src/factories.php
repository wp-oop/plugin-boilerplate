<?php

declare(strict_types=1);

use Dhii\Services\Factory;

return function (string $modDir): array {
    return [
        'me/plugin/demo/notice_text' => new Factory([
            'me/plugin/demo/plugin_title',
        ], function (string $pluginTitle): string {
            return sprintf(__('Modular plugin "%1$s" is active!', 'me-plugin'), $pluginTitle);
        }),

        'me/plugin/demo/plugin_title' => new Factory([
        ], function () {
            return 'OOP Plugin Demo';
        }),
    ];
};
