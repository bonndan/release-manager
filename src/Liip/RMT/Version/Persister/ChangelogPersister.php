<?php

namespace Liip\RMT\Version\Persister;

use Liip\RMT\Version\Persister\PersisterInterface;
use Liip\RMT\Changelog\ChangelogManager;

/**
 * Persists the changelog.
 * 
 */
class ChangelogPersister extends AbstractPersister implements PersisterInterface
{
    /**
     * changelog manager instance.
     * 
     * @var ChangelogManager
     */
    protected $changelogManager;

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
        return $this->getChangelogManager()->getCurrentVersion();
    }

    public function save($versionNumber)
    {
        $comment = $this->context->get('information-collector')->getValueFor('comment');
        $type = $this->context->get('information-collector')->getValueFor('type', null);
        $this->changelogManager->update($versionNumber, $comment, array('type' => $type));
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
    private function getChangelogManager()
    {
        if ($this->changelogManager === null) {
            // Create the changelog manager
            $this->changelogManager = new ChangelogManager(
                $this->context->getParam('project-root') . '/' . $this->options['location']
            );
        }
        
        return $this->changelogManager;
    }
}
