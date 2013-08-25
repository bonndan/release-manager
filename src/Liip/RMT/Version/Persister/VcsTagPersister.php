<?php

namespace Liip\RMT\Version\Persister;

use Liip\RMT\VCS\VCSInterface;
use Liip\RMT\Context;
use Liip\RMT\ContextAwareInterface;

class VcsTagPersister implements PersisterInterface, ContextAwareInterface
{
    protected $options = array();
    
    protected $versionRegex;
    protected $vcs;
    protected $prefix;
    
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
        $this->vcs = $this->context->get('vcs');
        $this->versionRegex = $this->context->getVersionGenerator()->getValidationRegex();
        if (isset($this->options['tag-pattern'])) {
            $this->versionRegex = $this->options['tag-pattern'];
        }
        $this->prefix = $this->generatePrefix(
            isset($this->options['tag-prefix']) ? $this->options['tag-prefix'] : '');
    }

    /**
     * @inheritdoc
     */
    public function getCurrentVersion()
    {
        $tags = $this->getValidVersionTags($this->versionRegex);
        if (count($tags)===0){
            throw new \Liip\RMT\Exception\NoReleaseFoundException(
                'No VCS tag matching the regex ['.$this->getTagPrefix().$this->versionRegex.']');
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

    public function getTagPrefix()
    {
        return $this->prefix;
    }

    public function getTagFromVersion($versionName)
    {
        return $this->getTagPrefix().$versionName;
    }

    public function getVersionFromTag($tagName)
    {
        return substr($tagName, strlen($this->getTagPrefix()));
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
     * @param $versionRegex
     */
    public function getValidVersionTags($versionRegex)
    {
        $validator = new TagValidator($versionRegex, $this->getTagPrefix());
        return $validator->filtrateList($this->vcs->getTags());
    }

    protected function generatePrefix($userTag){
        preg_match_all('/\{([^\}]*)\}/', $userTag, $placeHolders);
        foreach ($placeHolders[1] as $pos => $placeHolder){
            if ($placeHolder == 'branch-name'){
                $replacement = $this->vcs->getCurrentBranch();
            }
            else if ($placeHolder == 'date'){
                $replacement = date('Y-m-d');
            }
            else {
                throw new \Liip\RMT\Exception("There is no rules to process the prefix placeholder [$placeHolder]");
            }
            $userTag = str_replace($placeHolders[0][$pos], $replacement, $userTag);
        }
        return $userTag;
    }


}
