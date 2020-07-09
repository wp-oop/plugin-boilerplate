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
 * Text Domain: plugin
 * Domain Path: /languages
 */

(function (string $mainFile): void {
    $rootDir = dirname($mainFile);
    $autoload = "$rootDir/vendor/composer/autoload.php";

    if (file_exists($autoload)) {
        require $autoload;
    }

    add_action('plugins_loaded', function () use ($mainFile, $rootDir) {
        $incDir = "$rootDir/inc";
        $bootstrap = require "$incDir/bootstrap.php";

        $appContainer = $bootstrap($rootDir, $mainFile);
    });
})(__FILE__);
