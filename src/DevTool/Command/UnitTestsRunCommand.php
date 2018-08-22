<?php
declare(strict_types=1);

namespace Tale\DevTool\Command;

use Tale\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UnitTestsRunCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this->setName('unit-tests:run')
            ->addOption('coverage-text', null, InputOption::VALUE_NONE, 'Display coverage info?')
            ->addOption('coverage-html', null, InputOption::VALUE_OPTIONAL, 'Save coverage info as HTML?')
            ->addOption('coverage-clover', null, InputOption::VALUE_OPTIONAL, 'Save coverage info as XML?')
            ->addOption('testdox', null, InputOption::VALUE_NONE, 'Show detailed output in the testdox format?')
            ->addOption('filter', null, InputOption::VALUE_OPTIONAL, 'Name of specific tests to run', false)
            ->addOption('group', null, InputOption::VALUE_OPTIONAL, 'Excute only a tests group?', false)
            ->setDescription('Runs unit tests (phpunit).')
            ->setHelp('This command runs the unit tests');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $args = [
            '--verbose',
            '--configuration' => $this->getApplication()->getConfigFilePath('phpunit.xml'),
        ];

        if ($input->getOption('coverage-text')) {
            $args[] = '--coverage-text';
        }

        if ($path = $input->getOption('coverage-clover')) {
            $args['--coverage-clover'] = $path;
        }

        if ($path = $input->getOption('coverage-html')) {
            $args['--coverage-html'] = $path;
        }

        if ($input->getOption('testdox')) {
            $args['--testdox'] = true;
        }

        if ($filter = $input->getOption('filter')) {
            $args['--filter'] = $filter;
        }

        if ($group = $input->getOption('group')) {
            $args['--group'] = $group;
        }

        return $this->getApplication()->runUnitTests($args);
    }
}
