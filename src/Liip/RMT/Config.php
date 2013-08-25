<?php

namespace Liip\RMT;

/**
 * Config value object.
 * 
 * 
 */
class Config
{
    /**
     * git/hg
     * 
     * @var string
     */
    private $vcs;
    
    private $prerequisites;
    
    private $preReleaseActions;
    
    private $versionPersister = "vcs-tag";
    
    private $postReleaseActions;
    
    /**
     * Factory method.
     * 
     * @param type $data
     */
    public static function create($data)
    {
        $config = new Config;
        foreach ($data as $key => $entry) {
            if (method_exists($config, 'get' . $key)) {
                $config->$key = $entry;
            }
        }
        
        return $config;
    }
    
    public function getVcs()
    {
        return $this->vcs;
    }

    public function getPrerequisites()
    {
        return $this->prerequisites;
    }

    public function getPreReleaseActions()
    {
        return $this->preReleaseActions;
    }

    public function getVersionPersister()
    {
        return $this->versionPersister;
    }

    public function getPostReleaseActions()
    {
        return $this->postReleaseActions;
    }
    
    /**
     * Returns a std class with all properties.
     * 
     * @return stdClass
     */
    public function toJson()
    {
        $object = new \stdClass();
        $object->vcs                = $this->vcs;
        $object->prerequisites      = $this->prerequisites;
        $object->preReleaseActions  = $this->preReleaseActions;
        $object->versionPersister   = $this->versionPersister;
        $object->postReleaseActions = $this->postReleaseActions;
    
        return $object;
    }
    
    /**
     * Get interceptor.
     * 
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
    }
}