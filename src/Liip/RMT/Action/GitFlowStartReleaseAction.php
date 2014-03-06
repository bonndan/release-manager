<?php
namespace Liip\RMT\Action;

/**
 * Creates a new git flow release branch.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GitFlowStartReleaseAction extends GitFlowAction
{
    public function execute()
    {
        $newVersion = $this->context->getNewVersion();
        $this->getVCS()->startRelease($newVersion);
        $this->context->getOutput()->writeln('Created a new git flow release ' . $newVersion);
    }
}
