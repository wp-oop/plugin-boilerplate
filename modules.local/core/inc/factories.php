<?php

declare(strict_types=1);

use Dhii\Package\Version\StringVersionFactoryInterface;
use Dhii\Versions\StringVersionFactory;
use Me\Plugin\Core\FilePathPluginFactory;
use Psr\Container\ContainerInterface;
use WpOop\WordPress\Plugin\FilePathPluginFactoryInterface;
use WpOop\WordPress\Plugin\PluginInterface;

return function (string $rootDir, string $mainFile): array {
    return [
        'me/plugin/plugin' => function (ContainerInterface $c) use ($mainFile): PluginInterface {
            $f = $c->get('wordpress/plugin_factory');
            assert($f instanceof FilePathPluginFactoryInterface);
            $plugin = $f->createPluginFromFilePath($mainFile);

            return $plugin;
        },
        'me/plugin/plugin_factory' => function (ContainerInterface $c): FilePathPluginFactoryInterface {
            return $c->get('wordpress/plugin_factory');
        },
        'wordpress/plugin_factory' => function (ContainerInterface $c): FilePathPluginFactoryInterface {
            $factory = $c->get('package/version_factory');
            /** @var StringVersionFactoryInterface $factory */
            return new FilePathPluginFactory($factory);
        },
        'me/plugin/version_factory' => function (ContainerInterface $c): StringVersionFactoryInterface {
            return $c->get('package/version_factory');
        },
        'package/version_factory' => function (): StringVersionFactoryInterface {
            return new StringVersionFactory();
        },
    ];
};
