<?php
namespace Liip\RMT\Version\Persister;

use Liip\RMT\Helpers\TagValidator;
use Liip\RMT\Version;

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
     * @inheritdoc
     */
    public function save(Version $version)
    {
        $this->context->get('output')->writeln("Creation of a new VCS tag [<yellow>$version</yellow>]");
        $this->getVCS()->createTag($version);
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
