<?php
declare(strict_types=1);

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
    public function testConfigure(): void
    {
        $coverageCheck = new CoverageCheckCommand();
        self::assertSame('coverage:check', $coverageCheck->getName());
    }
}
