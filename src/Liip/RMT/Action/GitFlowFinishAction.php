<?php
namespace Liip\RMT\Action;

use InvalidArgumentException;
use Liip\RMT\Version\Detector\GitFlowBranch;

/**
 * Finishes the current git flow release or hotfix.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GitFlowFinishAction extends GitFlowAction
{
    /**
     * the git flow branch type
     * @var string
     */
    private $branchType;
    
    /**
     * Constructor
     * 
     * @param string $branchType
     * @throws InvalidArgumentException
     */
    public function __construct($branchType)
    {
        if (!in_array($branchType, array(GitFlowBranch::HOTFIX, GitFlowBranch::RELEASE))) {
            throw new InvalidArgumentException("Incorrect branch type given.");
        }
        $this->branchType = $branchType;
    }
    
    public function execute()
    {
        if ($this->branchType == GitFlowBranch::RELEASE) {
            $this->getVCS()->finishRelease();
        } else {
            $this->getVCS()->finishHotfix();
        }
        
        $this->confirmSuccess('Finished the ' . $this->branchType . '.');
    }
}
