<?php
namespace Liip\RMT\Version\Persister;

use Liip\RMT\Helpers\ComposerConfig;
use Liip\RMT\Version;
use Liip\RMT\Version\Persister\PersisterInterface;
use Liip\RMT\Version\Detector\DetectorInterface;

/**
 * Persists the version in the composer file.
 * 
 * 
 */
class ComposerPersister extends AbstractPersister implements PersisterInterface, DetectorInterface
{
    /**
     * composer config helper
     * 
     * @var ComposerConfig
     */
    private $helper;
    
    /**
     * @inheritdoc
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
     * Inject the composer configuration.
     * 
     * @param ComposerConfig $config
     */
    public function setConfig(ComposerConfig $config)
    {
        if ($config !== null) {
            $this->helper = $config;
        }
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



