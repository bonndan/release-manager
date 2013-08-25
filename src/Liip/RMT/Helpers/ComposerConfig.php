<?php
namespace Liip\RMT\Helpers;

use Liip\RMT\Context;

/**
 * Helper to read/manipulate the composer config file.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class ComposerConfig
{
    /**
     * project root path
     * 
     * @var string
     */
    private $composerFile;
    
    /**
     * Constructor.
     * 
     * @param \Liip\RMT\Context $context
     * @throws \Liip\RMT\Exception
     */
    public function __construct(Context $context)
    {
        $this->composerFile = $context->getParam('project-root') . '/composer.json';
        if (!file_exists($this->composerFile)) {
            throw new \Liip\RMT\Exception("The composer file is missing ($this->composerFile)");
        }
    }

    /**
     * Returns the data of the RMT config section.
     * 
     * @return array|null
     */
    public function getRMTConfigSection()
    {
        $json = json_decode(file_get_contents($this->composerFile));
        if (!isset($json->extra->rmt)) {
            return null;
        }
        
        return $json->extra->rmt;
    }
    
    /**
     * Replaces the version string using regex.
     * 
     * @param string $newVersion
     */
    public function replaceVersion($newVersion)
    {
        $fileContent = file_get_contents($this->composerFile);
        $fileContent = preg_replace('/("version":[^,]*,)/', '"version": "' . $newVersion . '",', $fileContent);
        file_put_contents($this->composerFile, $fileContent);
    }
}