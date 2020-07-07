<?php

declare(strict_types=1);

(function (string $baseFile) {
    $baseDir = dirname($baseFile);
    $rootDir = dirname($baseDir);
    define('ROOT_DIR', $rootDir);

    error_reporting(E_ALL | E_STRICT);

    require_once "$rootDir/vendor/autoload.php";
})(__FILE__);
