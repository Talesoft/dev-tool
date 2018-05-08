<?php

namespace Tale\DevTool;

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
        parent::__construct('Tale Dev Tool', '0.2.0');
        $this->configure();
    }

    protected function configure()
    {
        $this->add(new CheckCommand());
        $this->add(new CodeStyleCheckCommand());
        $this->add(new CodeStyleFixCommand());
        $this->add(new CoverageCheckCommand());
        $this->add(new CoverageReportCommand());
        $this->add(new InstallCommand());
        $this->add(new UnitTestsRunCommand());
    }

    public function getWorkingDirectory()
    {
        return getcwd();
    }

    protected function getConfigDirectory()
    {
        return realpath(__DIR__ . '/../../config');
    }

    public function getConfigFilePath($fileName)
    {
        $localPath = realpath($this->getWorkingDirectory().DIRECTORY_SEPARATOR.$fileName);

        if ($localPath) {
            return $localPath;
        }

        return $this->getConfigDirectory().DIRECTORY_SEPARATOR.$fileName;
    }

    public function isWindows()
    {
        return strncmp(strtolower(PHP_OS), 'win', 3) === 0;
    }

    public function isUnix()
    {
        return !$this->isWindows();
    }

    public function isHhvm()
    {
        return defined('HHVM_VERSION');
    }

    public function runCommand($command, OutputInterface $output, array $arguments = null)
    {
        $arguments = $arguments ?: [];

        $command = $this->find($command);
        $arguments['command'] = $command->getName();

        return $command->run(new ArrayInput($arguments), $output);
    }

    protected function getShellCommandPath($command)
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

    public function runShellCommand($command, array $arguments = null)
    {
        $arguments = $arguments ?: [];
        $parts = [escapeshellcmd($command)];

        foreach ($arguments as $key => $arg) {
            if (!is_int($key)) {
                $arg = "$key=$arg";
            }

            $parts[] = escapeshellarg($arg);
        }

        passthru(implode(' ', $parts), $returnCode);

        return is_numeric($returnCode) ? intval($returnCode) : $returnCode;
    }

    public function runVendorCommand($name, array $arguments = null)
    {
        return $this->runShellCommand($this->getShellCommandPath("vendor/bin/$name"), $arguments);
    }

    public function runUnitTests(array $arguments = null)
    {
        return $this->runVendorCommand('phpunit', $arguments);
    }

    public function runCodeStyleChecker(array $arguments = null)
    {
        $arguments = $arguments ?: [];

        $arguments[] = '--colors';

        return $this->runVendorCommand('phpcs', $arguments);
    }

    public function runCodeStyleFixer(array $arguments = null)
    {
        return $this->runVendorCommand('phpcbf', $arguments);
    }

    public function runCoverageReporter(array $arguments = null)
    {
        return $this->runVendorCommand('test-reporter', $arguments);
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->configure();

        return parent::run($input, $output);
    }
}
