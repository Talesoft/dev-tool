<?php
declare(strict_types=1);

namespace Tale\DevTool;

use Symfony\Component\Console\Exception\LogicException;
use Tale\DevTool\Command\CheckCommand;
use Tale\DevTool\Command\CodeStyleCheckCommand;
use Tale\DevTool\Command\CodeStyleFixCommand;
use Tale\DevTool\Command\CoverageCheckCommand;
use Tale\DevTool\Command\CoverageReportCommand;
use Tale\DevTool\Command\InstallCommand;
use Tale\DevTool\Command\UnitTestsRunCommand;
use RuntimeException;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    public function __construct()
    {
        parent::__construct('Tale Dev Tool', '0.2.4');
        $this->configure();
    }

    /**
     *
     * @throws LogicException
     */
    protected function configure(): void
    {
        $this->add(new CheckCommand());
        $this->add(new CodeStyleCheckCommand());
        $this->add(new CodeStyleFixCommand());
        $this->add(new CoverageCheckCommand());
        $this->add(new CoverageReportCommand());
        $this->add(new InstallCommand());
        $this->add(new UnitTestsRunCommand());
    }

    public function getWorkingDirectory(): string
    {
        return getcwd();
    }

    protected function getConfigDirectory(): string
    {
        return \dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config';
    }

    public function getConfigFilePath($fileName): string
    {
        $localPath = realpath($this->getWorkingDirectory().DIRECTORY_SEPARATOR.$fileName);

        if ($localPath) {
            return $localPath;
        }

        return $this->getConfigDirectory().DIRECTORY_SEPARATOR.$fileName;
    }

    public function isWindows(): bool
    {
        return strncmp(strtolower(PHP_OS), 'win', 3) === 0;
    }

    public function isUnix(): bool
    {
        return !$this->isWindows();
    }

    /**
     * @param $command
     * @param OutputInterface $output
     * @param array|null $arguments
     * @return int
     * @throws \Symfony\Component\Console\Exception\CommandNotFoundException
     */
    public function runCommand($command, OutputInterface $output, array $arguments = null): int
    {
        $arguments = $arguments ?: [];

        $command = $this->find($command);
        $arguments['command'] = $command->getName();

        try {
            return $command->run(new ArrayInput($arguments), $output);
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * @param $command
     * @return bool|string
     * @throws \RuntimeException
     */
    protected function getShellCommandPath($command): string
    {
        $cwd = $this->getWorkingDirectory();
        $commandPath = $cwd."/$command";

        //Check if there is a windows batch file equivalent for composer commands
        if ($this->isWindows() && ($batPath = realpath("$commandPath.bat"))) {
            $commandPath = $batPath;
        }

        if (!($commandPath = realpath($commandPath))) {
            throw new RuntimeException(
                "The given command [$command] was not found"
            );
        }

        return $commandPath;
    }

    public function runShellCommand($command, array $arguments = null): int
    {
        $arguments = $arguments ?: [];
        $parts = [escapeshellcmd($command)];

        foreach ($arguments as $key => $arg) {
            if (!\is_int($key)) {
                $arg = "$key=$arg";
            }

            $parts[] = escapeshellarg($arg);
        }

        passthru(implode(' ', $parts), $returnCode);

        return is_numeric($returnCode) ? (int)$returnCode : $returnCode;
    }

    /**
     * @param $name
     * @param array|null $arguments
     * @return int
     * @throws \RuntimeException
     */
    public function runVendorCommand($name, array $arguments = null): int
    {
        return $this->runShellCommand($this->getShellCommandPath("vendor/bin/$name"), $arguments);
    }

    /**
     * @param array|null $arguments
     * @return int
     * @throws \RuntimeException
     */
    public function runUnitTests(array $arguments = null): int
    {
        return $this->runVendorCommand('phpunit', $arguments);
    }

    /**
     * @param array|null $arguments
     * @return int
     * @throws \RuntimeException
     */
    public function runCodeStyleChecker(array $arguments = null): int
    {
        $arguments = $arguments ?: [];

        $arguments[] = '--colors';

        return $this->runVendorCommand('phpcs', $arguments);
    }

    /**
     * @param array|null $arguments
     * @return int
     * @throws \RuntimeException
     */
    public function runCodeStyleFixer(array $arguments = null): int
    {
        return $this->runVendorCommand('phpcbf', $arguments);
    }

    /**
     * @param array|null $arguments
     * @return int
     * @throws \RuntimeException
     */
    public function runCoverageReporter(array $arguments = null): int
    {
        return $this->runVendorCommand('test-reporter', $arguments);
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws \Exception
     */
    public function run(InputInterface $input = null, OutputInterface $output = null): int
    {
        $this->configure();

        return parent::run($input, $output);
    }
}
