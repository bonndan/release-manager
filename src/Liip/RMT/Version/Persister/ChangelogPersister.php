<?php

namespace Liip\RMT\Version\Persister;

use Liip\RMT\Version\Persister\PersisterInterface;
use Liip\RMT\Changelog\Changelog;

/**
 * Persists the changelog.
 * 
 */
class ChangelogPersister extends AbstractPersister implements PersisterInterface
{
    /**
     * changelog manager instance.
     * 
     * @var Changelog
     */
    private $changelog;

    /**
     * constructor options
     * 
     * @var array
     */
    private $options = array();
    
    /**
     * Constructor.
     * 
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        // Define a default changelog name
        if (!array_key_exists('location', $options)) {
            $options['location'] = 'CHANGELOG';
        }

        $this->options = $options;
    }

    public function getCurrentVersion()
    {
        return $this->getChangelog()->getCurrentVersion();
    }

    public function save($versionNumber)
    {
        $comment = $this->context->get('information-collector')->getValueFor('comment');
        $type = $this->context->get('information-collector')->getValueFor('type', null);
        $this->changelog->addVersion($versionNumber, $comment, array());
    }

    public function getInformationRequests()
    {
        return array('comment');
    }

    /**
     * Creates the changelog manager if necessary.
     * 
     * @todo refactor: inject.
     * @return ChangelogManager
     */
    private function getChangelog()
    {
        if ($this->changelog === null) {
            // Create the changelog manager
            $this->changelog = new Changelog(
                $this->context->getParam('project-root') . '/' . $this->options['location']
            );
        }
        
        return $this->changelog;
    }
}
