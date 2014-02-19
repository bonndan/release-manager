<?php

namespace Liip\RMT\Version\Detector;

use Liip\RMT\Exception;
use Liip\RMT\VCS\Git;
use Liip\RMT\Version;
use vierbergenlars\SemVer\SemVerException;

/**
 * Detects the "current" version of a git flow branch.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GitFlowBranch implements DetectorInterface
{
    const RELEASE = 'release';
    const HOTFIX = 'hotfix';
    
    /**
     * git
     * 
     * @var Git 
     */
    private $git;

    /**
     * Branch type (release|hotfix)
     * 
     * @var string
     */
    private $branchType;
    
    /**
     * Constructor.
     * 
     * @param Git $git
     */
    public function __construct(Git $git, $branchType)
    {
        $this->git = $git;
        $this->branchType = $branchType;
    }

    public function getCurrentVersion()
    {
        $branch = $this->git->getCurrentBranch();
        if (strpos($branch, $this->branchType . '/') !== 0) {
            throw new Exception('Expected to find "' . $this->branchType . '/" at beginning of branch name.');
        }

        try {
            $version = new Version(str_replace($this->branchType . "/", "", $branch));
        } catch (SemVerException $ex) {
            throw new Exception('Cannot detect version: ' . $ex->getMessage());
        }
        
        return $version;
    }

}
