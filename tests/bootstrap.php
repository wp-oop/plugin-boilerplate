<?php

(function (string $baseFile) {
    $baseDir = dirname($baseFile);
    $rootDir = dirname($baseDir);

    error_reporting(E_ALL | E_STRICT);

    require_once "$rootDir/vendor/autoload.php";
})(__FILE__);
