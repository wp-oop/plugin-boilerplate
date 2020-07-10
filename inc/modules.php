<?php

return function (string $rootDir, string $mainFile): iterable {
    $modulesDir = "$rootDir/modules";
    $localModulesDir = "$rootDir/modules.local";

    $modules = [
        (require "$localModulesDir/core/module.php")($rootDir, $mainFile),
    ];

    return $modules;
};
