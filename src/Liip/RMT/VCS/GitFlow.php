<?php
namespace Liip\RMT\VCS;

use Liip\RMT\Version\Detector\GitFlowBranch;
use Liip\RMT\Version;

/**
 * Git controlled by git flow
 *
 * @author daniel
 */
class GitFlow extends Git
{
    protected $dryRunCommandWords = array('tag', 'push', 'add', 'commit', 'flow');
    
    /**
     * Start a git flow release.
     * 
     * @param Version $version
     * @return array output
     */
    public function startRelease(Version $version)
    {
        $command = "flow release start " . $version;
        return $this->executeGitCommand($command);
    }
    
    /**
     * Finishes the current git flow release.
     * 
     * @return array
     * @throws Exception
     */
    public function finishRelease($comment)
    {
        $detector = new GitFlowBranch($this, GitFlowBranch::RELEASE);
        $version = $detector->getCurrentVersion();
        $command = 'flow release finish -F -m "' . $comment . '" ' . $version;
        return $this->executeGitCommand($command);
    }
    
    /**
     * Start a git flow hotfix.
     * 
     * @param Version $version
     * @return array output
     */
    public function startHotfix(Version $version)
    {
        $command = "flow hotfix start " . $version;
        return $this->executeGitCommand($command);
    }
    
    /**
     * Finishes the current git flow release.
     * 
     * @return array
     * @throws Exception
     */
    public function finishHotfix($comment)
    {
        $detector = new GitFlowBranch($this, GitFlowBranch::HOTFIX);
        $version = $detector->getCurrentVersion();
        $command = 'flow hotfix finish -F -m "' . $comment . '" ' . $version;
        return $this->executeGitCommand($command);
    }
}
