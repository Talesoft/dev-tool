<?php
declare(strict_types=1);

namespace Tale\DevTool\Command;

use Tale\DevTool\AbstractCommand;
use SimpleXMLElement;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CoverageCheckCommand extends AbstractCommand
{
    public const DEFAULT_REQUIRED_COVERAGE = 80;

    protected function configure(): void
    {
        $this->setName('coverage:check')
            ->addArgument('input-file', InputArgument::REQUIRED, 'The XML file to check coverage on')
            ->addOption('required-coverage', null, InputOption::VALUE_OPTIONAL, 'The minimum coverage to pass', 80)
            ->setDescription('Checks coverage.')
            ->setHelp('This command checks coverage');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $xmlFile = realpath($input->getArgument('input-file'));
        $requiredCoverage = (int)$input->getOption('required-coverage');

        if (!$xmlFile) {
            $output->writeln('<fg=red>Error: Code coverage files not passed. Please pass input-file.</>');
            return 1;
        }

        $output->writeln('Validating code coverage...');

        $xml = new SimpleXMLElement(file_get_contents($xmlFile));
        $metrics = $xml->xpath('//metrics');
        $totalElements = 0;
        $checkedElements = 0;

        foreach ($metrics as $metric) {
            $totalElements += (int)$metric['elements'];
            $checkedElements += (int)$metric['coveredelements'];
        }

        $coverage = ($checkedElements / $totalElements) * 100;

        if ($coverage < $requiredCoverage) {
            $output->writeln(
                "<fg=red>Fail: Code coverage is {$coverage}%. "
                ."You need to reach {$requiredCoverage}% to validate this build.</>"
            );

            return 1;
        }

        $output->writeln("<fg=green>Pass: Code Coverage {$coverage}%!</>");

        return 0;
    }
}
