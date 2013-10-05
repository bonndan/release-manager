<?php
namespace Liip\RMT\Version\Persister;

use Liip\RMT\Version\Persister\PersisterInterface;

/**
 * Persists the version in the composer file.
 * 
 * 
 */
class ComposerPersister extends AbstractPersister implements PersisterInterface
{
    /**
     * composer config helper
     * 
     * @var \Liip\RMT\Helpers\ComposerConfig
     */
    private $helper;
    
    /**
     * Returns the current version as stored in the composer file.
     * 
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->getHelper()->getCurrentVersion();
    }

    /**
     * Saves the version to the composer file.
     * 
     * @param string $versionNumber
     */
    public function save($versionNumber)
    {
        $this->getHelper()->setVersion($versionNumber);
    }

    /**
     * Returns the composer file helper.
     * 
     * @return \Liip\RMT\Helpers\ComposerConfig
     */
    private function getHelper()
    {
        if ($this->helper === null) {
            $this->helper = new \Liip\RMT\Helpers\ComposerConfig($this->context);
        }
        
        return $this->helper;
    }
}



