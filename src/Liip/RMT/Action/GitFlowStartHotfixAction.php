<?php
namespace Liip\RMT\Action;

use Liip\RMT\Action\BaseAction;

/**
 * Creates a new git flow release branch.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GitFlowStartHotfixAction extends BaseAction
{
    public function execute()
    {
        $newVersion = $this->context->getNewVersion();
        $git = $this->context->getVCS(); /* @var $git \Liip\RMT\VCS\GitFlow */
        $git->startHotfix($newVersion);
        $this->context->getOutput()->writeln('Created a new git flow hotfix ' . $newVersion);
    }
}
