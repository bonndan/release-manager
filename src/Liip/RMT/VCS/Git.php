<?php
namespace Liip\RMT\VCS;

use Liip\RMT\Exception;
use Liip\RMT\Version;

/**
 * Git VCS
 * 
 */
class Git extends BaseVCS
{
    protected $dryRun = false;
    protected $dryRunCommandWords = array('tag', 'push', 'add', 'commit');
    
    /**
     * the last executed command (for testing purposes)
     * @var string|null
     */
    public $lastCommand;

    public function getAllModificationsSince($tag, $color=true)
    {
        return $this->executeGitCommand("log --oneline $tag..HEAD ".($color?'--color=always':''));
    }

    public function getModifiedFilesSince($tag)
    {
        $data = $this->executeGitCommand("diff --name-status $tag..HEAD");
        $files = array();
        foreach($data as $d) {
            $parts = explode("\t", $d);
            $files[$parts[1]] = $parts[0];
        }
        return $files;
    }

    public function getLocalModifications(){
        return $this->executeGitCommand('status -s');
    }


    public function getTags()
    {
        return $this->executeGitCommand("tag");
    }

    public function createTag(Version $tagName)
    {
        return $this->executeGitCommand("tag $tagName");
    }

    public function publishTag($tagName)
    {
        $this->executeGitCommand("push origin $tagName");
    }

    public function publishChanges()
    {
        $this->executeGitCommand("push origin ".$this->getCurrentBranch());
    }

    public function saveWorkingCopy($commitMsg='')
    {
        $this->executeGitCommand("add --all");
        $this->executeGitCommand("commit -m \"$commitMsg\"");
    }

    public function getCurrentBranch()
    {
        $branches = $this->executeGitCommand('branch');
        foreach ($branches as $branch){
            if (strpos($branch, '* ') === 0 && !preg_match('/^\*\s\(.*\)$/', $branch)){
                return substr($branch,2);
            }
        }
        throw new Exception("Not currently on any branch");
    }
    
    /**
     * Enable the dry-run mode
     * 
     * @param boolean $flag
     */
    public function setDryRun($flag)
    {
        $this->dryRun = $flag;
    }

    protected function executeGitCommand($cmd)
    {
        // Avoid using some commands in dry mode
        if ($this->dryRun){
            if ($cmd !== 'tag'){
                $cmdWords = explode(' ', $cmd);
                if (in_array($cmdWords[0], $this->dryRunCommandWords)){
                    $this->lastCommand = $cmd;
                    return $cmd;
                }
            }
        }

        // Execute
        $cmd = 'git '.$cmd;
        exec($cmd, $result, $exitCode);
        if ($exitCode !== 0){
            throw new Exception('Error while executing git command: '.$cmd);
        }
        
        $this->lastCommand = $cmd;
        return $result;
    }
}

