<?php
namespace Liip\RMT\Command;

use Liip\RMT\Action\GitFlowFinishReleaseAction;
use Liip\RMT\Context;
use Liip\RMT\Version\Detector\GitFlowReleaseBranch;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that eases releasing with "git flow".
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class FinishCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('finish');
        $this->setDescription('Finishes what has begun with the "start" command');
        $this->setHelp('The <comment>finish</comment> interactive task must be used with git flow after "start".');

    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $detector = new GitFlowReleaseBranch($this->getContext()->getVCS());
        $newVersion = $detector->getCurrentVersion();
        $this->getContext()->setNewVersion($newVersion);
        $action = new GitFlowFinishReleaseAction();
        $action->setContext($this->getContext());
        $this->getContext()->addToList(Context::PRERELEASE_LIST, $action);
    }
}
