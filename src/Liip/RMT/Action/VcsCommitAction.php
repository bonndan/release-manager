<?php

namespace Liip\RMT\Action;

use Liip\RMT\Exception;

/**
 * Commit everything
 */
class VcsCommitAction extends BaseAction
{
    /**
     * flag to enable graceful fails
     * @var boolean
     */
    private $failsGracefully = false;
    
    public function execute()
    {
        try {
            $this->context->getVCS()->saveWorkingCopy(
                'Release of new version '. $this->context->getParam('new-version')
            );
        } catch (Exception $ex) {
            if ($this->failsGracefully) {
                $this->confirmSuccess('Failed gracefully: ' . $ex->getMessage());
                return;
            }
            
            throw $ex;
        }
        
        $this->confirmSuccess();
    }
    
    /**
     * Toggle graceful failing.
     * 
     * @param boolean $flag
     */
    public function setFailsGracefully($flag)
    {
        $this->failsGracefully = (boolean) $flag;
    }
}

