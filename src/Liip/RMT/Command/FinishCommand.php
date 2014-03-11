<?php
namespace Liip\RMT\Command;

use Liip\RMT\Action\GitFlowFinishAction;
use Liip\RMT\Action\VcsCommitAction;
use Liip\RMT\Context;
use Liip\RMT\Information\InformationCollector;
use Liip\RMT\Version\Detector\GitFlowBranch;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that eases releasing with "git flow".
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class FinishCommand extends ReleaseCommand
{
    protected function configure()
    {
        $this->setName('finish');
        $this->setDescription('Finishes a start or hotfix command.');
        $this->setHelp('The <comment>finish</comment> interactive task must be used with git flow after "start" or "hotfix".');

        $this->loadInformationCollector();

        // Register the command option
        foreach ($this->getContext()->getInformationCollector()->getCommandOptions() as $option) {
            $this->getDefinition()->addOption($option);
        }
    }
    
    protected function loadInformationCollector()
    {
        $ic = new InformationCollector();

        // Register options of all lists (prerequistes and actions)
        foreach (array('prerequisites', 'preReleaseActions', 'postReleaseActions') as $listName){
            foreach ($this->getContext()->getList($listName) as $listItem){
                $ic->registerRequests($listItem->getInformationRequests());
            }
        }
        
        $this->getContext()->setService('information-collector', $ic);
    }
    
    /**
     * Adds the vcs-commit action to the post-release action list if not present.
     * 
     * @return void
     */
    private function ensureVCSCommitIsPostReleaseAction()
    {
        $postRelease = $this->getContext()->getList(Context::POSTRELEASE_LIST);
        foreach ($postRelease as $action) {
            if ($action instanceof VcsCommitAction) {
                return;
            }
        }
        
        $action = new VcsCommitAction();
        $action->setContext($this->getContext());
        $postRelease->push($action);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ensureVCSCommitIsPostReleaseAction();
        
        $detector = new GitFlowBranch($this->getContext()->getVCS());
        $newVersion = $detector->getCurrentVersion();
        $this->getContext()->setNewVersion($newVersion);
        
        $currentVersion = $this->getContext()->getVersionDetector()->getCurrentVersion();
        $type = $currentVersion->getDifferenceType($newVersion);
        $this->getContext()->setParameter('type', $type);
        
        //in case the type information is needed...
        $this->getContext()->getInformationCollector()->registerStandardRequest('type');
        $this->getContext()->getInformationCollector()->setValueFor('type', $type);
        
        //push a git flow finish action to the post release list
        $action = new GitFlowFinishAction($detector->getBranchType());
        $action->setContext($this->getContext());
        $this->getContext()->getList(Context::POSTRELEASE_LIST)->push($action);
        
        parent::execute($input, $output);
    }
}
