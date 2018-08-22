<?php
declare(strict_types=1);

namespace Tale\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Tale\DevTool\Application;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ApplicationTest.
 *
 * @coversDefaultClass \Tale\DevTool\Application
 */
class ApplicationTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::configure
     */
    public function testApplication(): void
    {
        $app = new Application();

        self::assertInstanceOf(ConsoleApplication::class, $app);
        self::assertTrue($app->has('check'));
        self::assertTrue($app->has('code-style:check'));
        self::assertTrue($app->has('code-style:fix'));
        self::assertTrue($app->has('coverage:check'));
        self::assertTrue($app->has('coverage:report'));
        self::assertTrue($app->has('install'));
        self::assertTrue($app->has('unit-tests:run'));
    }

    /**
     * @covers ::getWorkingDirectory
     */
    public function testGetWorkingDirectory(): void
    {
        $app = new Application();
        self::assertSame(getcwd(), $app->getWorkingDirectory());
    }

    /**
     * @covers ::getConfigDirectory
     * @covers ::getConfigFilePath
     */
    public function testGetConfigFilePath(): void
    {
        $app = new Application();
        self::assertSame(realpath(__DIR__ . '/../../config/phpdoc.xml'), $app->getConfigFilePath('phpdoc.xml'));
        self::assertSame(realpath(__DIR__ . '/../../phpunit.xml'), $app->getConfigFilePath('phpunit.xml'));
    }

    /**
     * @covers ::isWindows
     * @covers ::isUnix
     */
    public function testEnvironmentCheck(): void
    {
        $app = new Application();
        self::assertSame(strncmp(strtolower(PHP_OS), 'win', 3) === 0, $app->isWindows());
        self::assertSame(strncmp(strtolower(PHP_OS), 'win', 3) !== 0, $app->isUnix());
    }

    /**
     * @covers ::runCommand
     */
    public function testRunCommand(): void
    {
        $app = new Application();
        $buffer = new BufferedOutput();
        $app->runCommand('list', $buffer);

        self::assertRegExp('/Available commands:[^:]+\scheck\s/', $buffer->fetch());
    }

    /**
     * @covers ::runShellCommand
     */
    public function testRunShellCommand(): void
    {
        $app = new Application();
        self::assertSame(0, $app->runShellCommand('composer', ['--quiet']));
        self::assertSame(0, $app->runShellCommand('composer', ['--quiet', '--verbose', '--format' => 'xml']));
    }

    /**
     * @covers ::getShellCommandPath
     * @covers ::runVendorCommand
     */
    public function testRunVendorCommand(): void
    {
        $app = new Application();
        $this->expectOutputRegex('/^PHP_CodeSniffer version/');
        self::assertSame(0, $app->runVendorCommand('phpcs', ['--version']));
    }

    /**
     * @covers                   ::getShellCommandPath
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The given command [vendor/bin/doNotExists] was not found
     */
    public function testGetShellCommandPathException(): void
    {
        $app = new Application();
        $app->runVendorCommand('doNotExists');
    }

    /**
     * @covers ::getShellCommandPath
     */
    public function testBatPath(): void
    {
        $cwd = getcwd();
        $appPath = __DIR__ . '/../app';
        chdir($appPath);
        foreach (glob($appPath.'/vendor/bin/*') as $file) {
            chmod($file, 0777);
        }
        $app = new WindowsApplicationTest();
        $path = $app->getPhpcsPath();
        chdir($cwd);

        self::assertSame(realpath($appPath.'/vendor/bin/phpcs.bat'), $path);
    }

    /**
     * @covers ::runUnitTests
     */
    public function testRunUnitTests(): void
    {
        $app = new Application();
        $this->expectOutputRegex('/^PHPUnit/');
        $code = $app->runUnitTests(['--version']);

        self::assertSame(0, $code);
    }

    /**
     * @covers ::runCodeStyleChecker
     */
    public function testRunCodeStyleChecker(): void
    {
        $app = new Application();
        $this->expectOutputRegex('/^PHP_CodeSniffer version/');
        self::assertSame(0, $app->runCodeStyleChecker(['--version']));
    }

    /**
     * @covers ::runCodeStyleFixer
     */
    public function testRunCodeStyleFixer(): void
    {
        $app = new Application();
        $this->expectOutputRegex('/^PHP_CodeSniffer version/');
        self::assertSame(0, $app->runCodeStyleFixer(['--version']));
    }

    /**
     * @covers ::runCoverageReporter
     */
    public function testRunCoverageReporter(): void
    {
        $app = new Application();
        $this->expectOutputRegex('/Code Climate PHP Test Reporter/');
        self::assertSame(0, $app->runCoverageReporter(['--version']));
    }

    /**
     * @covers ::run
     * @throws \Exception
     */
    public function testRun(): void
    {
        $input = new StringInput('code-style:check -n');
        $buffer = new BufferedOutput();
        $app = new Application();
        $app->setAutoExit(false);

        self::assertSame(0, $app->run($input, $buffer));
        self::assertSame('Code looks great. Go on!', trim($buffer->fetch()));
    }
}
