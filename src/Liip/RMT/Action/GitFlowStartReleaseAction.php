<?php
namespace Liip\RMT\Action;

use Liip\RMT\Action\BaseAction;

/**
 * Creates a new git flow release branch.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GitFlowStartReleaseAction extends BaseAction
{
    public function execute()
    {
        $newVersion = $this->context->getNewVersion();
        $git = $this->context->getVCS();
        $git->startRelease($newVersion);
        $this->context->getOutput()->writeln('Created a new git flow release ' . $newVersion);
    }
}
