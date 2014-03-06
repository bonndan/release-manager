<?php
namespace Liip\RMT\Action;

/**
 * GitFlowAction is the base class for git flow based actions.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
abstract class GitFlowAction extends BaseAction
{
    /**
     * Returns the vcs.
     * 
     * @return \Liip\RMT\Action\GitFlow
     * @throws \LogicException
     */
    protected function getVCS()
    {
        $vcs = $this->context->getVCS();
        if (!$vcs instanceof \Liip\RMT\VCS\GitFlow) {
            if ($vcs instanceof \Liip\RMT\VCS\Git) {
                return new \Liip\RMT\VCS\GitFlow();
            }
            throw new \LogicException('Neither Git nor GitFlow are configured as VCS.');
        }
        
        return $vcs;
    }
}
