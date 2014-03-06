<?php
namespace Liip\RMT\Action;

/**
 * Creates a new git flow release branch.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GitFlowStartHotfixAction extends GitFlowAction
{
    public function execute()
    {
        $newVersion = $this->context->getNewVersion();
        $this->getVCS()->startHotfix($newVersion);
        $this->context->getOutput()->writeln('Created a new git flow hotfix ' . $newVersion);
    }
}
