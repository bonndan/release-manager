<?php
namespace Liip\RMT\Action;

use Liip\RMT\Action\BaseAction;

/**
 * Finishes the current git flow release.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GitFlowFinishReleaseAction extends BaseAction
{
    public function execute()
    {
        $comment = $this->context->getInformationCollector()->getValueFor('comment');
        $this->getVCS()->finishRelease($comment);
        $this->context->getOutput()->writeln('Finish the git flow release.');
    }
    
    /**
     * Returns the vcs.
     * 
     * @return \Liip\RMT\Action\GitFlow
     * @throws \LogicException
     */
    private function getVCS()
    {
        $vcs = $this->context->getVCS();
        if (!$vcs instanceof \Liip\RMT\VCS\GitFlow) {
            
            if ($vcs instanceof \Liip\RMT\VCS\Git) {
                return new \Liip\RMT\VCS\GitFlow();
            }
            
            throw new \LogicException('GitFlow is not configured as VCS.');
        }
        
        return $vcs;
    }
}
