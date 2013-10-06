<?php
namespace Liip\RMT\Version\Persister;

use Liip\RMT\Helpers\TagValidator;

/**
 * VCS tag persister.
 * 
 */
class VcsTagPersister extends AbstractPersister implements PersisterInterface
{
    /**
     * @inheritdoc
     */
    public function getCurrentVersion()
    {
        return $this->getVCS()->getCurrentVersion();
    }

    /**
     * 
     * @param type $versionNumber
     */
    public function save($versionNumber)
    {
        $this->context->get('output')->writeln("Creation of a new VCS tag [<yellow>$versionNumber</yellow>]");
        $this->getVCS()->createTag($versionNumber);
    }

    /**
     * Return all tags matching the versionRegex and prefix
     * 
     * @return array
     */
    public function getValidVersionTags()
    {
        $validator = new TagValidator();
        return $validator->filtrateList($this->getVCS()->getTags());
    }
    
    /**
     * Returns the vcs.
     * 
     * @return \Liip\RMT\VCS\VCSInterface
     */
    private function getVCS()
    {
        return $this->context->getVCS();
    }
}
