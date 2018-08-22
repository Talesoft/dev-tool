<?php
declare(strict_types=1);

namespace Tale\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Tale\DevTool\Application;
use Tale\DevTool\Command\UnitTestsRunCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Class UnitTestsRunCommandTest.
 *
 * @coversDefaultClass \Tale\DevTool\Command\UnitTestsRunCommand
 */
class UnitTestsRunCommandTest extends TestCase
{
    private static function remove($entity): void
    {
        if (is_file($entity)) {
            unlink($entity);
        }

        foreach (scandir($entity, SCANDIR_SORT_NONE) as $file) {
            if ($file !== '.' && $file !== '..') {
                self::remove($entity.'/'.$file);
            }
        }
    }

    /**
     * @covers ::configure
     */
    public function testConfigure(): void
    {
        $unitTests = new UnitTestsRunCommand();

        self::assertSame('unit-tests:run', $unitTests->getName());
    }

    /**
     * @covers ::execute
     * @throws \Exception
     */
    public function testExecute(): void
    {
        $cwd = getcwd();
        $app = realpath(__DIR__ . '/../../app');
        chdir($app);
        foreach (glob(__DIR__.'/../../app/vendor/bin/*') as $file) {
            chmod($file, 0777);
        }
        $coverageHtml = $app.DIRECTORY_SEPARATOR.'coverage';
        $coverageClover = $app.DIRECTORY_SEPARATOR.'coverage.xml';
        $input = new ArrayInput([
            'command' => 'unit-tests:run',
            '--coverage-text' => true,
            '--coverage-html' => $coverageHtml,
            '--coverage-clover' => $coverageClover
        ]);
        $buffer = new ConsoleOutput();
        $app = new Application();
        $app->setAutoExit(false);
        ob_start();
        $code = $app->run($input, $buffer);
        $contents = ob_get_clean();
        self::assertSame(0, $code);
        $data = json_decode($contents);
        self::assertTrue($data->{'--coverage-text'});
        self::assertSame($coverageHtml, $data->{'--coverage-html'});
        self::assertSame($coverageClover, $data->{'--coverage-clover'});
        if (file_exists($coverageHtml)) {
            self::remove($coverageHtml);
        }
        if (file_exists($coverageClover)) {
            unlink($coverageClover);
        }
        chdir($cwd);
    }
}
