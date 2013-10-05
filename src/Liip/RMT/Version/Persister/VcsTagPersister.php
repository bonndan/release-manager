<?php
namespace Liip\RMT\Version\Persister;

use Liip\RMT\Helpers\TagValidator;
use Liip\RMT\Context;

/**
 * VCS tag persister.
 * 
 */
class VcsTagPersister extends AbstractPersister implements PersisterInterface
{
    /**
     * vcs instance
     * 
     * @var \Liip\RMT\VCS\VCSInterface
     */
    protected $vcs;

    /**
     * Inject the context.
     * 
     * @param \Liip\RMT\Context $context
     */
    public function setContext(Context $context)
    {
        parent::setContext($context);
        $this->vcs = $this->context->getVCS();
    }

    /**
     * @inheritdoc
     */
    public function getCurrentVersion()
    {
        $tags = $this->getValidVersionTags();
        if (count($tags) === 0) {
            throw new \Liip\RMT\Exception\NoReleaseFoundException(
            'No VCS tag matching a semantic version.');
        }

        // Extract versions from tags and sort them
        $versions = $this->getVersionFromTags($tags);
        usort($versions, array($this->context->getVersionGenerator(), 'compareTwoVersions'));

        return array_pop($versions);
    }

    public function save($versionNumber)
    {
        $tagName = $this->getTagFromVersion($versionNumber);
        $this->context->get('output')->writeln("Creation of a new VCS tag [<yellow>$tagName</yellow>]");
        $this->vcs->createTag($tagName);
    }

    public function getTagFromVersion($versionName)
    {
        return $versionName;
    }

    public function getVersionFromTag($tagName)
    {
        return $tagName;
    }

    public function getVersionFromTags($tags)
    {
        $versions = array();
        foreach ($tags as $tag) {
            $versions[] = $this->getVersionFromTag($tag);
        }
        return $versions;
    }

    public function getCurrentVersionTag()
    {
        return $this->getTagFromVersion($this->getCurrentVersion());
    }

    /**
     * Return all tags matching the versionRegex and prefix
     * 
     * @return array
     */
    public function getValidVersionTags()
    {
        $validator = new TagValidator();
        return $validator->filtrateList($this->vcs->getTags());
    }

}
