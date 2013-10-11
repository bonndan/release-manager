<?php

namespace Liip\RMT\Action;

use Liip\RMT\Changelog\Changelog;

/**
 * Update the changelog file.
 * 
 * The default file name is "changelog.xml".
 */
class ChangelogUpdateAction extends BaseAction
{
    protected $options;

    public function __construct($options)
    {
        $this->options = array_merge(array(
            'file' => 'changelog.xml'
        ), $options);
    }

    public function execute()
    {
        $changelog = new Changelog($this->options['file']);
        $changelog->addVersion(
            $this->context->getParam('new-version'),
            $this->context->getInformationCollector()->getValueFor('comment'),
            $this->getCommits()
        );
        
        $this->confirmSuccess();
    }

    public function getInformationRequests()
    {
        return array('comment');
    }
    
    /**
     * 
     * @return array
     */
    private function getCommits()
    {
        $rawCommits = $this->context->getVCS()->getAllModificationsSince(
            $this->context->getVersionPersister()->getCurrentVersion(),
            false
        );
        $commits = array();
        foreach ($rawCommits as $line) {
            $tmp = explode(' ', $line);
            $hash = array_shift($tmp);
            $commits[$hash] = trim(implode(' ', $tmp));
        }

        return $commits;
    }
}

