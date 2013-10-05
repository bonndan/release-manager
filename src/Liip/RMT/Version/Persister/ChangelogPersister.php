<?php

namespace Liip\RMT\Version\Persister;

use Liip\RMT\Version\Persister\PersisterInterface;
use Liip\RMT\ContextAwareInterface;
use Liip\RMT\Context;
use Liip\RMT\Changelog\ChangelogManager;

/**
 * Persists the changelog.
 * 
 */
class ChangelogPersister implements PersisterInterface, ContextAwareInterface
{
    /**
     * changelog manager instance.
     * 
     * @var ChangelogManager
     */
    protected $changelogManager;

    /**
     * the context
     * 
     * @var \Liip\RMT\Context 
     */
    private $context;
    
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

    /**
     * Inject the context.
     * 
     * @param \Liip\RMT\Context $context
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
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
