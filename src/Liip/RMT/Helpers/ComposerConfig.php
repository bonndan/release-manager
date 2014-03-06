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
        $json = $this->getJson();
        if (!isset($json->extra->rmt)) {
            return null;
        }

        $config = \Liip\RMT\Config::create($json->extra->rmt);
        return $config;
    }
    
    /**
     * Returns the project name.
     * 
     * @return string|null
     */
    public function getProjectName()
    {
        $json = $this->getJson();
        if (!isset($json->name)) {
            return null;
        }
        
        return $json->name;
    }

    /**
     * Writes the passed config into the composer file.
     * 
     * @param Config $config
     * @return string the serialized json
     */
    public function addRMTConfigSection(Config $config)
    {
        $json = $this->getJson();
        if (!isset($json->extra)) {
            $json->extra = new \stdClass();
        }
        $json->extra->rmt = $config->toJson();
        return $this->save($json);
    }

    /**
     * Sets the new version
     * 
     * @param string $newVersion
     */
    public function setVersion($newVersion)
    {
        $json = $this->getJson();
        $json->version = (string)$newVersion;
        return $this->save($json);
    }

    /**
     * Returns the current version as stored in the composer file.
     * 
     * @return string|null
     */
    public function getCurrentVersion()
    {
        $json = $this->getJson();
        if (!isset($json->version)) {
            return null;
        }
        
        return $json->version;
    }
    
    /**
     * Returns the decoded json.
     * 
     * @return object
     */
    private function getJson()
    {
        return json_decode(file_get_contents($this->composerFile));
    }
    
    /**
     * Saves the json object back to the composer file.
     * 
     * @param object $json
     * @return string the serialized content
     */
    private function save($json)
    {
        $serialized = JSONHelper::format(json_encode($json));
        $fixed = str_replace('"_empty_":', '"":', $serialized);
        file_put_contents($this->composerFile, $fixed);
        return $serialized;
    }
}