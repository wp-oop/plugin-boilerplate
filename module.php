<?php

declare(strict_types=1);

use Dhii\Modular\Module\ModuleInterface;
use Me\Plugin\ModularModule;

return function (string $rootDir, string $mainFile): ModuleInterface {
    $modules = (require "$rootDir/inc/modules.php")($rootDir, $mainFile);
    $module = new ModularModule($modules);

    return $module;
};
