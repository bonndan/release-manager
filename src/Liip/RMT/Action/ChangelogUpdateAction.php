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
        $commits = array();
        $currentVersion = $this->context->getVersionDetector()->getCurrentVersion();
        try {
            $rawCommits = $this->context->getVCS()->getAllModificationsSince(
                $currentVersion,
                false
            );
            foreach ($rawCommits as $line) {
                $tmp = explode(' ', $line);
                $hash = array_shift($tmp);
                $commits[$hash] = trim(implode(' ', $tmp));
            }
        } catch (\Liip\RMT\Exception $exception) {
            $output = $this->context->get('output'); /* @var $output \Liip\RMT\Output\Output */
            $output->writeln('<error>Error fetching commits since version ' . $currentVersion . '. Version not in VCS?</error>');
        }
        
        return $commits;
    }
}

