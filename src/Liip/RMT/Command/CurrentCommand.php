<?php

namespace Liip\RMT\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Outputs current version.
 * 
 * 
 */
class CurrentCommand extends BaseCommand
{

    protected function configure()
    {
        $this->setName('current');
        $this->setDescription('Display information about the current release');
        $this->setHelp('The <comment>current</comment> task can be used to display information on the current release');
        $this->addOption('raw', null, InputOption::VALUE_NONE, 'display only the version name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContext();
        $version = $this->context->getVersionPersister()->getCurrentVersion();
        if ($input->getOption('raw') == true) {
            $output->writeln($version);
        } else {
            $msg = "Current release is: <green>$version</green>";
            $output->writeln($msg);
        }
    }
}

