<?php

declare(strict_types=1);

namespace Me\Plugin\Test;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use function Brain\Monkey\Functions\when;

/**
 * Base class for project tests.
 */
class AbstractTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();

        // __()
        when('__')
            ->returnArg();
    }

    public function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
