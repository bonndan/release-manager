<?php

namespace Liip\RMT\Helpers;

use Liip\RMT\Context;
use Liip\RMT\ContextAwareInterface;

/**
 * Helps finding the implementations for abbreviated service names in the configuration.
 * 
 */
class ServiceBuilder
{
    /**
     * @var Context
     */
    private $context;
    
    /**
     * Constructor.
     * 
     * @param \Liip\RMT\Helpers\Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }
    
    /**
     * Returns a service.
     * 
     * @param mixed $rawConfig
     * @param string $sectionName
     * @return object
     * @throws \Liip\RMT\Config\Exception
     */
    public function getService($rawConfig, $sectionName)
    {
        if (is_string($rawConfig)) {
            return $this->getClassAndOptionsByString($rawConfig, $sectionName);
        } 
        
        if (is_object($rawConfig)) {
            $rawConfig = (array)$rawConfig;
        }
        
        if (is_array($rawConfig)) {
            if (!isset($rawConfig['name'])) {
                throw new \Liip\RMT\Config\Exception("Missing information for [$sectionName], you must provide a [name] value");
            }
            
            $name = $rawConfig['name'];
            unset($rawConfig['name']);
            return $this->getClassAndOptionsByString($name, $sectionName, $rawConfig);
            
        }
        
        throw new \Liip\RMT\Config\Exception(
            "Invalid configuration for [$sectionName] must be a class name or an array with name and options."
            . ' Is: ' . var_export($rawConfig, true)
        );
    }
    
    /**
     * Resolves the class name by string.
     * 
     * @param string $name
     * @param string $sectionName
     * @return array
     */
    private function getClassAndOptionsByString($name, $sectionName, array $options = array())
    {
        $class = $name;
        if (strpos($name, '\\') === false) {
            $class = $this->findInternalClass($name, $sectionName);
        }
        
        return $this->instanciateObject($class, $options);
    }

    /**
     * Sub part of the normalize()
     */
    private function findInternalClass($name, $sectionName)
    {
        // Remove list id like xxx_3
        $classType = $sectionName;
        if (strpos($classType, '_') !== false) {
            $classType = substr($classType, 0, strpos($classType, '_'));
        }

        // Guess the namespace
        $namespacesByType = array(
            '' => '',
            'vcs' => 'Liip\RMT\VCS',
            'prerequisites' => 'Liip\RMT\Prerequisite',
            'preReleaseActions' => 'Liip\RMT\Action',
            'postReleaseActions' => 'Liip\RMT\Action',
            "versionPersister" => 'Liip\RMT\Version\Persister'
        );
        $nameSpace = $namespacesByType[$classType];

        // Guess the class name
        // Convert from xxx-yyy-zzz to XxxYyyZzz and append suffix
        $suffixByType = array(
            '' => '',
            'vcs' => '',
            'prerequisites' => '',
            'preReleaseActions' => 'Action',
            'postReleaseActions' => 'Action',
            "versionPersister" => 'Persister'
        );
        $nameSpace = $namespacesByType[$classType];
        $className = str_replace(' ', '', ucwords(str_replace('-', ' ', $name))) . $suffixByType[$classType];

        if (class_exists($nameSpace . '\\' . $className)) {
            return $nameSpace . '\\' . $className;
        }
        
        throw new \Liip\RMT\Config\Exception("Cannot resolve " . $nameSpace . '\\' . $className);
    }

    /**
     * Instantiates the object.
     * 
     * @param string $className
     * @param string $options
     * @return object
     */
    protected function instanciateObject($className, array $options = array())
    {
        $object = new $className($options);
        if ($object instanceof ContextAwareInterface) {
            $object->setContext($this->context);
        }
        
        return $object;
    }
}