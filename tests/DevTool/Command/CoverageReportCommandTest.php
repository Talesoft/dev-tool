<?php

namespace Tale\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Tale\DevTool\Command\CoverageReportCommand;

/**
 * Class CoverageReportCommandTest.
 *
 * @coversDefaultClass \Tale\DevTool\Command\CoverageReportCommand
 */
class CoverageReportCommandTest extends TestCase
{
    /**
     * @covers ::configure
     */
    public function testConfigure()
    {
        $coverageReport = new CoverageReportCommand();
        self::assertSame('coverage:report', $coverageReport->getName());
    }
}
