<?php

namespace Tale\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Tale\DevTool\Command\CoverageCheckCommand;

/**
 * Class CoverageCheckCommandTest.
 *
 * @coversDefaultClass \Tale\DevTool\Command\CoverageCheckCommand
 */
class CoverageCheckCommandTest extends TestCase
{
    /**
     * @covers ::configure
     */
    public function testConfigure()
    {
        $coverageCheck = new CoverageCheckCommand();

        self::assertSame('coverage:check', $coverageCheck->getName());
    }
}
