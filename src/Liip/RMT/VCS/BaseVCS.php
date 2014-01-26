<?php

namespace Liip\RMT\VCS;

use Liip\RMT\Helpers\TagValidator;
use Liip\RMT\VCS\VCSInterface;
use Liip\RMT\Version;

/**
 * Abstract class for vcs implementations.
 * 
 */
abstract class BaseVCS implements VCSInterface
{

    protected $options;

    public function __construct($options = array())
    {
        $this->options = $options;
    }

    /**
     * Returns the highest valid version tag.
     * 
     * @return Version
     */
    public function getCurrentVersion()
    {
        $tags = $this->getValidVersionTags();
        if (count($tags) === 0) {
            return Version::createInitialVersion();
        }

        usort($tags, array("vierbergenlars\\SemVer\\version", "compare"));

        return new Version(array_pop($tags));
    }

    /**
     * Return all tags matching the versionRegex and prefix
     * 
     * @return array
     */
    private function getValidVersionTags()
    {
        $validator = new TagValidator();
        $valid = $validator->filtrateList($this->getTags());
        
        $versions = array();
        foreach ($valid as $versionNumber) {
            $versions[] = new Version($versionNumber);
        }
        return $versions;
    }
}

