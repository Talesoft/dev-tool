<?php

namespace Tale\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Tale\DevTool\Command\CodeStyleFixCommand;

/**
 * Class CodeStyleFixCommandTest.
 *
 * @coversDefaultClass \Tale\DevTool\Command\CodeStyleFixCommand
 */
class CodeStyleFixCommandTest extends TestCase
{
    /**
     * @covers ::configure
     */
    public function testConfigure()
    {
        $codeStyleFix = new CodeStyleFixCommand();
        self::assertSame('code-style:fix', $codeStyleFix->getName());
    }
}
