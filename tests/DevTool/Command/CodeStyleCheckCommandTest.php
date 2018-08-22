<?php
declare(strict_types=1);

namespace Tale\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Tale\DevTool\Application;
use Tale\DevTool\Command\CodeStyleCheckCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class CodeStyleCheckCommandTest.
 *
 * @coversDefaultClass \Tale\DevTool\Command\CodeStyleCheckCommand
 */
class CodeStyleCheckCommandTest extends TestCase
{
    /**
     * @covers ::configure
     */
    public function testConfigure(): void
    {
        $codeStyleCheck = new CodeStyleCheckCommand();
        self::assertSame('code-style:check', $codeStyleCheck->getName());
    }

    /**
     * @covers ::execute
     * @throws \Exception
     */
    public function testExecute(): void
    {
        $cwd = getcwd();
        chdir(__DIR__ . '/../../app');
        $input = new StringInput('code-style:check');
        $buffer = new BufferedOutput();
        $app = new Application();
        $app->setAutoExit(false);
        $code = $app->run($input, $buffer);
        chdir($cwd);

        self::assertSame(0, $code);
        self::assertRegExp('/Code looks great\. Go on!/', $buffer->fetch());

        $cwd = getcwd();
        chdir(__DIR__ . '/../../app');
        $input = new StringInput('code-style:check --ignore-debug --ignore-tests');
        $buffer = new BufferedOutput();
        $app = new Application();
        $app->setAutoExit(false);
        $code = $app->run($input, $buffer);
        chdir($cwd);

        self::assertSame(0, $code);
        self::assertRegExp('/Code looks great\. Go on!/', $buffer->fetch());
    }
}
