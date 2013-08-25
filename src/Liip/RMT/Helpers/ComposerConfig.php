<?php

namespace Liip\RMT\Helpers;

use Liip\RMT\Context;
use Liip\RMT\Config;

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
    public function __construct(Context $context = null)
    {
        if ($context !== null) {
            $this->setComposerFile($context->getParam('project-root') . '/composer.json');
        }
    }

    /**
     * Set the path to the composer file.
     * 
     * @param string $file
     * @throws \Liip\RMT\Exception
     */
    public function setComposerFile($file)
    {
        if (!file_exists($file)) {
            throw new \Liip\RMT\Exception("The composer file is missing ($file)");
        }
        $this->composerFile = $file;
    }

    /**
     * Returns the data of the RMT config section.
     * 
     * @return \Liip\RMT\Config|null
     */
    public function getRMTConfigSection()
    {
        $json = json_decode(file_get_contents($this->composerFile));
        if (!isset($json->extra->rmt)) {
            return null;
        }

        $config = \Liip\RMT\Config::create($json->extra->rmt);
        return $config;
    }

    /**
     * Writes the passed config into the composer file.
     * 
     * @param Config $config
     * @return string the serialized json
     */
    public function addRMTConfigSection(Config $config)
    {
        $json = json_decode(file_get_contents($this->composerFile));
        if (!isset($json->extra)) {
            $json->extra = new \stdClass();
        }
        $json->extra->rmt = $config->toJson();
        $serialized = JSONHelper::format(json_encode($json));
        file_put_contents($this->composerFile, $serialized);
        return $serialized;
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