<?php

namespace Liip\RMT\Version\Detector;

use Liip\RMT\Exception;
use Liip\RMT\VCS\Git;
use Liip\RMT\Version;
use vierbergenlars\SemVer\SemVerException;

/**
 * Detects the "current" version of a git flow release branch.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GitFlowReleaseBranch implements DetectorInterface
{
    /**
     * git
     * @var Git 
     */
    private $git;

    /**
     * Constructor.
     * 
     * @param Git $git
     */
    public function __construct(Git $git)
    {
        $this->git = $git;
    }

    public function getCurrentVersion()
    {
        $branch = $this->git->getCurrentBranch();
        if (strpos($branch, 'release/') !== 0) {
            throw new Exception('Expected to find "release/" at beginning of branch name.');
        }

        try {
            $version = new Version(str_replace("release/", "", $branch));
        } catch (SemVerException $ex) {
            throw new Exception('Cannot finish release: ' . $ex->getMessage());
        }
        
        return $version;
    }

}
