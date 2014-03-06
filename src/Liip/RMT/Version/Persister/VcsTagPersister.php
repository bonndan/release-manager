<?php
namespace Liip\RMT\Version\Persister;

use Liip\RMT\Helpers\TagValidator;
use Liip\RMT\VCS\VCSInterface;
use Liip\RMT\Version;
use Liip\RMT\Version\Detector\DetectorInterface;

/**
 * VCS tag persister.
 * 
 */
class VcsTagPersister extends AbstractPersister implements PersisterInterface, DetectorInterface
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
        $this->context->getOutput()->writeln("Creation of a new VCS tag [<yellow>$version</yellow>]");
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
     * @return VCSInterface
     */
    private function getVCS()
    {
        return $this->context->getVCS();
    }
}
