<?php
declare(strict_types=1);

namespace Tale\DevTool\Command;

use Tale\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodeStyleFixCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('code-style:fix')
            ->setDescription('Runs code style fixer (phpcbf).')
            ->setHelp('This command runs the code style fixer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $args = [
            '--standard' => $this->getApplication()->getConfigFilePath('phpcs.xml'),
        ];

        return $this->getApplication()->runCodeStyleFixer($args);
    }
}
