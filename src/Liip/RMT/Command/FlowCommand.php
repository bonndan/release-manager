<?php
namespace Liip\RMT\Command;

use Liip\RMT\Action\GitFlowFinishReleaseAction;
use Liip\RMT\Action\GitFlowStartReleaseAction;
use Liip\RMT\Context;
use Liip\RMT\Exception;
use Liip\RMT\Version\Detector\GitFlowReleaseBranch;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command the eases interaction with "git flow".
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class FlowCommand extends ReleaseCommand
{
    protected function configure()
    {
        $this->setName('flow');
        $this->setDescription('Release with the flow.');
        $this->setHelp('The <comment>flow</comment> interactive task must be used with git flow');

        $this->loadInformationCollector();

        // Register the command option
        
        foreach ($this->getContext()->getInformationCollector()->getCommandOptions() as $option) {
            $this->getDefinition()->addOption($option);
        }
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $detector = new GitFlowReleaseBranch($this->getContext()->getVCS());
        try {
            $newVersion = $detector->getCurrentVersion();
            $this->getContext()->setNewVersion($newVersion);
            $action = new GitFlowFinishReleaseAction();
        } catch (Exception $ex) {
            $action = new GitFlowStartReleaseAction();
            
        }
        
        $action->setContext($this->getContext());
        $this->getContext()->addToList(Context::PRERELEASE_LIST, $action);
            
        parent::execute($input, $output);
    }
}
