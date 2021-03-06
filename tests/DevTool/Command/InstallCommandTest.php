<?php
declare(strict_types=1);

namespace Tale\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Tale\DevTool\Command\InstallCommand;

/**
 * Class InstallCommandTest.
 *
 * @coversDefaultClass \Tale\DevTool\Command\InstallCommand
 */
class InstallCommandTest extends TestCase
{
    /**
     * @covers ::configure
     */
    public function testConfigure(): void
    {
        $install = new InstallCommand();
        self::assertSame('install', $install->getName());
    }
}
