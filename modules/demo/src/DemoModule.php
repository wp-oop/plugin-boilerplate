<?php

declare(strict_types=1);

namespace Me\Plugin\Demo;

use Dhii\Container\ServiceProvider;
use Dhii\Modular\Module\ModuleInterface;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

/**
 * A demo module.
 *
 * Demonstrates how a module may be added.
 */
class DemoModule implements ModuleInterface
{
    /**
     * @inheritDoc
     */
    public function setup(): ServiceProviderInterface
    {
        $srcDir = __DIR__;
        $rootDir = dirname($srcDir);

        return new ServiceProvider(
            (require "$srcDir/factories.php")($rootDir),
            (require "$srcDir/extensions.php")()
        );
    }

    /**
     * @inheritDoc
     */
    public function run(ContainerInterface $c): void
    {
        /** @var string $noticeText */
        $noticeText = $c->get('me/plugin/demo/notice_text');
        add_action('admin_notices', function () use ($noticeText): void {
            ?>
            <div class="notice notice-info">
                <p><?php echo $noticeText ?></p>
            </div>
            <?php
        });
    }
}
