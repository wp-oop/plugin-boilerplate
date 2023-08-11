<?php
/**
 * My WordPress plugin.
 *
 * @wordpress-plugin
 *
 * Plugin Name: My WordPress Plugin
 * Description: A project skeleton useful for starting a new WordPress plugin
 * Version: 0.1.0-alpha1
 * Author: Me
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: me-plugin
 * Domain Path: /languages
 */

use Dhii\Container\Dictionary;
use Dhii\Modular\Module\ModuleInterface;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Me\Plugin\MainModule;

(function (string $mainFile, string $wpRootDir): void {
    $baseDir = dirname($mainFile);
    $srcDir = "$baseDir/src";
    $autoload = "$baseDir/vendor/autoload.php";
    if (file_exists($autoload)) {
        require $autoload;
    }

    add_action('plugins_loaded', function () use ($mainFile, $baseDir, $wpRootDir) {
        $appModule = new MainModule($mainFile, $baseDir);
        $modules = array_merge((require "$baseDir/src/modules.php")($mainFile, $baseDir), [$appModule]);

        # WP APIs
        require_once( "$wpRootDir/wp-admin/includes/plugin.php" );

        /**
         * Manipulate the list of this plugin's modules.
         *
         * @param array<ModuleInterface> $modules The list of plugin modules.
         */
        $modules = apply_filters('my_plugin_modules', $modules);
        // Retrieve each module's service provider
        $providers = array_map(function(ModuleInterface $module): ServiceProviderInterface { return $module->setup(); }, $modules);

        /** @var callable(iterable<ServiceProviderInterface>, ?array<ContainerInterface>): ContainerInterface $bootstrap */
        $bootstrap = require "$baseDir/src/bootstrap.php";
        $container = $bootstrap($providers, [
            new Dictionary([
                'me/plugin/main_file_path' => $mainFile,
                'me/plugin/basedir' => $baseDir,
                'wp/core/abspath' => $wpRootDir,
            ]),
        ]);

        // Run each module
        array_walk($modules, function(ModuleInterface $module) use ($container) { $module->run($container); });
    });
})(
    __FILE__,
    ABSPATH
);
