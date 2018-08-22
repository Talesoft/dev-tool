<?php
declare(strict_types=1);

namespace Tale\DevTool\Command;

use Tale\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CoverageReportCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this->setName('coverage:report')
            ->setDescription('Reports coverage.')
            ->setHelp('This command reports coverage')
            ->addArgument('input-file', InputArgument::REQUIRED, 'The XML file to report coverage from')
            ->addOption(
                'php-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'If specified, the report is only sent for the given PHP version'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $xmlFile = realpath($input->getArgument('input-file'));
        $phpVersion = $input->getOption('php-version');

        if (!empty($phpVersion)) {
            if (!preg_match('/^'.preg_quote($phpVersion, '/').'(\D.*)?$/', PHP_VERSION)) {
                $output->writeln(
                    'Test report ignored since PHP version ('.PHP_VERSION.')'.
                    ' does not match '.$phpVersion.'.'
                );

                return 0;
            }
            $output->writeln(
                '<fg=green>Proceed test report since PHP version ('.PHP_VERSION.') '.
                'matches '.$phpVersion.'.</>'
            );
        }

        $this->getApplication()->runVendorCommand('test-reporter', [
            '--coverage-report' => $xmlFile,
        ]);

        return 0;
    }
}
