<?php

namespace Liip\RMT\Version\Persister;

use Liip\RMT\Version\Persister\PersisterInterface;
use Liip\RMT\Changelog\Changelog;
use Liip\RMT\Version;
use Liip\RMT\Version\Detector\DetectorInterface;

/**
 * Persists the changelog.
 * 
 */
class ChangelogPersister extends AbstractPersister implements PersisterInterface, DetectorInterface
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

    /**
     * @inheritdoc
     */
    public function save(Version $version)
    {
        $comment = $this->context->getInformationCollector()->getValueFor('comment');
        $type = $this->context->getInformationCollector()->getValueFor('type', null);
        $this->changelog->addVersion($version, $comment, array());
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
