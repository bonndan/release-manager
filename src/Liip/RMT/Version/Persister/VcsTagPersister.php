<?php
namespace Liip\RMT\Version\Persister;

use Liip\RMT\Context;
use Liip\RMT\ContextAwareInterface;
use Liip\RMT\Helpers\TagValidator;

/**
 * VCS tag persister.
 * 
 */
class VcsTagPersister implements PersisterInterface, ContextAwareInterface
{

    protected $options = array();
    protected $versionRegex;
    
    /**
     * vcs instance
     * 
     * @var \Liip\RMT\VCS\VCSInterface
     */
    protected $vcs;

    /**
     * the context
     * 
     * @var Context
     */
    protected $context;

    /**
     * Constructor.
     * 
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * Inject the context.
     * 
     * @param \Liip\RMT\Context $context
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
        $this->vcs = $this->context->getVCS();
        $this->versionRegex = $this->context->getVersionGenerator()->getValidationRegex();
        if (isset($this->options['tag-pattern'])) {
            $this->versionRegex = $this->options['tag-pattern'];
        }
    }

    /**
     * @inheritdoc
     */
    public function getCurrentVersion()
    {
        $tags = $this->getValidVersionTags();
        if (count($tags) === 0) {
            throw new \Liip\RMT\Exception\NoReleaseFoundException(
            'No VCS tag matching the regex [' . $this->versionRegex . ']');
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

    public function init()
    {
        
    }

    public function getInformationRequests()
    {
        return array();
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
