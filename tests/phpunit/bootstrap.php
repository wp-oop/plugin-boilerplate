<?php

declare(strict_types=1);

(function (string $baseFile) {
    $baseDir = dirname($baseFile);
    $rootDir = dirname($baseDir, 2);
    define('BASE_DIR', $rootDir);
    define('BASE_PATH', "$rootDir/plugin.php");
    define('ABSPATH', '/var/www/html');

    error_reporting(E_ALL | E_STRICT);

    require_once "$rootDir/vendor/autoload.php";
    require_once "$rootDir/vendor/antecedent/patchwork/Patchwork.php";
})(__FILE__);
