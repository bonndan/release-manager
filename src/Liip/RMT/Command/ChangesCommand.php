<?php

namespace Liip\RMT\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that shows the last changes.
 * 
 * 
 */
class ChangesCommand extends BaseCommand
{

    protected function configure()
    {
        $this->setName('changes');
        $this->setDescription('Shows the changes since last release');
        $this->setHelp('The <comment>changes</comment> command is used to list the changes since last release.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = new \Liip\RMT\Action\DisplayLastChanges();
        $context = $this->getContext();
        $context->setService('output', $output);
        $action->setContext($context);
        
        $this->output->writeln($action->getTitle());
        $action->execute();
    }
}

