<?php
namespace Liip\RMT\Version\Persister;

use Liip\RMT\Helpers\ComposerConfig;
use Liip\RMT\Version;
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
     * @var ComposerConfig
     */
    private $helper;
    
    /**
     * Constructor.
     * 
     * @param ComposerConfig $helper
     */
    public function __construct(ComposerConfig $helper = null)
    {
        if ($helper !== null) {
            $this->helper = $helper;
        }
    }
    
    /**
     * Returns the current version as stored in the composer file.
     * 
     * @return string
     */
    public function getCurrentVersion()
    {
        $versionNumber = $this->getHelper()->getCurrentVersion();
        if ($versionNumber === null) {
            $versionNumber = Version::INITIAL;
        }
        
        return new Version($versionNumber);
    }

    /**
     * @inheritdoc
     */
    public function save(Version $version)
    {
        $this->getHelper()->setVersion($version);
    }

    /**
     * Returns the composer file helper.
     * 
     * @return ComposerConfig
     */
    private function getHelper()
    {
        if ($this->helper === null) {
            $this->helper = new ComposerConfig($this->context);
        }
        
        return $this->helper;
    }
}



