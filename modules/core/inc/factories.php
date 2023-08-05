<?php

declare(strict_types=1);

use Dhii\Package\Version\StringVersionFactoryInterface;
use Dhii\Services\Factories\Alias;
use Dhii\Services\Factory;
use Dhii\Versions\StringVersionFactory;
use Me\Plugin\Core\FilePathPluginFactory;
use WpOop\WordPress\Plugin\FilePathPluginFactoryInterface;
use WpOop\WordPress\Plugin\PluginInterface;

return function (string $rootDir, string $mainFile): array {
    return [
        'me/plugin/plugin' => new Factory([
            'wordpress/plugin_factory',
        ], function (FilePathPluginFactoryInterface $factory) use ($mainFile): PluginInterface {
            return $factory->createPluginFromFilePath($mainFile);
        }),
        'me/plugin/plugin_factory'  => new Alias('wordpress/plugin_factory'),
        'wordpress/plugin_factory' => new Factory([
            'package/version_factory',
        ], function (StringVersionFactoryInterface $factory): FilePathPluginFactoryInterface {
            return new FilePathPluginFactory($factory);
        }),
        'me/plugin/version_factory' => new Alias('package/version_factory'),
        'package/version_factory' => new Factory([
        ], function () {
            return new StringVersionFactory();
        }),
    ];
};
