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
     * @param Git    $git
     * @param string $branchType limit detection to a branch type
     */
    public function __construct(Git $git, $branchType = null)
    {
        $this->git = $git;
        $this->branchType = $branchType;
    }

    /**
     * Detects the current version based on branch name.
     * 
     * @return \Liip\RMT\Version
     * @throws Exception
     */
    public function getCurrentVersion()
    {
        /*
         * exception is not caught if a branch type is defined. 
         */
        if ($this->branchType !== null) {
            return $this->detect($this->branchType);
        }
        
        try {
            $version = $this->detect(self::RELEASE);
            $this->branchType = self::RELEASE;
            return $version;
        } catch (Exception $ex) {

        }
        
        try {
            $version = $this->detect(self::HOTFIX);
            $this->branchType = self::HOTFIX;
            return $version;
        } catch (Exception $ex) {

        }
        
        throw new Exception('Cannot detect release or hotfix branch.');
    }
    
    /**
     * Detects a version based on branch type.
     * 
     * @param string $branchType
     * @return \Liip\RMT\Version
     * @throws Exception
     */
    private function detect($branchType)
    {
        $branch = $this->git->getCurrentBranch();
        if (strpos($branch, $branchType . '/') !== 0) {
            throw new Exception('Expected to find "' . $branchType . '/" at beginning of branch name.');
        }
        
        try {
            $version = new Version(str_replace($branchType . "/", "", $branch));
        } catch (SemVerException $ex) {
            throw new Exception('Cannot detect version in branch name: ' . $ex->getMessage());
        }
        
        return $version;
    }

    /**
     * Checks if the current branch is a release or hotfix branch.
     * 
     * @return boolean
     */
    public function isInTheFlow()
    {
        $branch = $this->git->getCurrentBranch();
        return (strpos($branch, self::RELEASE . '/') === 0 || strpos($branch, self::HOTFIX . '/') === 0);
    }
    
    /**
     * Returns the set or autodetected branch type.
     * 
     * @return string
     */
    public function getBranchType()
    {
        return $this->branchType;
    }
}
