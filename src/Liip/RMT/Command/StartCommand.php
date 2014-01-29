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
 * Command that eases releasing with "git flow".
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class StartCommand extends ReleaseCommand
{
    protected function configure()
    {
        $this->setName('start');
        $this->setDescription('Release with the flow.');
        $this->setHelp('The <comment>start</comment> interactive task must be used with git flow');

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
        } catch (Exception $ex) {
            $action = new GitFlowStartReleaseAction();
            $action->setContext($this->getContext());
            $this->context->getList(Context::PRERELEASE_LIST)->unshift($action);
            parent::execute($input, $output);
            return;
        }
        
        throw new Exception("Detected a git flow release branch. Finish the current release first.");
    }
}
