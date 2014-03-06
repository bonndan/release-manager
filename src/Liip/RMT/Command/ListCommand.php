<?php
namespace Liip\RMT\Command;

use Liip\RMT\Helpers\ComposerConfig;

/**
 * Description of ListCommand
 *
 * @author daniel
 */
class ListCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('list');
        $this->setDescription('Lists all commands.');
        $this->setHelp('The <comment>list</comment> shows all commands and options.');
    }
    
    // Always executed
    protected function initialize($input, $output)
    {
        $helper = new ComposerConfig();
        $file   = $this->getApplication()->getProjectRootDir() . '/composer.json';
        $helper->setComposerFile($file);
        $config = $helper->getRMTConfigSection();
        if (!$config) {
            return;
        }
        
        $this->writeBigTitle('Current: ' . $helper->getProjectName() . ' ' . $helper->getCurrentVersion());
        $this->writeEmptyLine();
    }
 
    // Always executed, but first initialize and interact have already been called
    protected function execute($input, $output)
    {
        $command = new \Symfony\Component\Console\Command\ListCommand();
        $this->getApplication()->add($command);
        $command->run($input, $output);
    }
}
