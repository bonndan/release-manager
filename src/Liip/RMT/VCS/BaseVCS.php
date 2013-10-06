<?php

namespace Liip\RMT\VCS;

use Liip\RMT\Helpers\TagValidator;
use Liip\RMT\VCS\VCSInterface;

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
     * @return string
     * @throws \Liip\RMT\Exception\NoReleaseFoundException
     */
    public function getCurrentVersion()
    {
        $tags = $this->getValidVersionTags();
        if (count($tags) === 0) {
            throw new \Liip\RMT\Exception\NoReleaseFoundException(
            'No VCS tag matching a semantic version.');
        }

        usort($tags, array($this, 'compareTwoVersions'));

        return array_pop($tags);
    }

    /**
     * Return all tags matching the versionRegex and prefix
     * 
     * @return array
     */
    private function getValidVersionTags()
    {
        $validator = new TagValidator();
        return $validator->filtrateList($this->getTags());
    }

    public function compareTwoVersions($a, $b)
    {
        list($majorA, $minorA, $patchA) = explode('.', $a);
        list($majorB, $minorB, $patchB) = explode('.', $b);
        if ($majorA !== $majorB) {
            return $majorA < $majorB ? -1 : 1;
        }
        if ($minorA !== $minorB) {
            return $minorA < $minorB ? -1 : 1;
        }
        if ($patchA !== $patchB) {
            return $patchA < $patchB ? -1 : 1;
        }
        return 0;
    }

}

