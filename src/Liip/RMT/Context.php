<?php

namespace Liip\RMT;

use Liip\RMT\ContextAwareInterface;
use Liip\RMT\Config\Handler;

/**
 * Application context.
 * 
 * 
 */
class Context
{
    protected $services = array();
    protected $params = array();
    protected $lists = array();

    /**
     * Context factory method.
     * 
     * @param \Liip\RMT\Application $application
     * @return \Liip\RMT\Context
     */
    public static function create(Application $application)
    {
        $rootDir       = $application->getProjectRootDir();
        $configHandler = new Handler($application->getConfig(), $rootDir);
        $config        = $configHandler->getBaseConfig();
        $context       = new Context();

        // Select a branch specific config if a VCS is in use
        if (isset($config['vcs'])) {
            $context->setService('vcs', $config['vcs']['class'], $config['vcs']['options']);
            $vcs = $context->get('vcs');
            $branch = $vcs->getCurrentBranch();
            $config = $configHandler->getConfigForBranch($branch);
        }

        // Store the config for latter usage
        $context->setParameter('config', $config);

        /*
         * Populate the context the version generator
         */
        $generator =new \Liip\RMT\Version\Generator\SemanticGenerator();
        $generator->setContext($context);
        $context->setService("version-generator", $generator);
        
        //populate version persister
        foreach (array("version-persister") as $service){
            $context->setService($service, $config[$service]['class'], $config[$service]['options']);
        }
        
        foreach (array("prerequisites", "pre-release-actions", "post-release-actions") as $listName){
            $context->createEmptyList($listName);
            foreach ($config[$listName] as $service){
                $context->addToList($listName, $service['class'], $service['options']);
            }
        }

        // Provide the root dir as a context parameter
        $context->setParameter('project-root', $rootDir);
        return $context;
    }
    
    /**
     * Inject a service.
     * 
     * @param type $id
     * @param type $classOrObject
     * @param type $options
     * @throws \InvalidArgumentException
     */
    public function setService($id, $classOrObject, $options = null)
    {
        if (is_object($classOrObject)){
            $this->services[$id] = $classOrObject;
        }
        else if (is_string($classOrObject)) {
            $this->validateClass($classOrObject);
            $this->services[$id] = array($classOrObject, $options);
        }
        else {
            throw new \InvalidArgumentException("setService() only accept an object or a valid class name");
        }
    }

    /**
     * Returns a service.
     * 
     * @param string $id
     * @return object
     * @throws \InvalidArgumentException
     */
    public function getService($id)
    {
        if (!isset($this->services[$id])){
            throw new \InvalidArgumentException("There is no service define with id [$id]");
        }
        if (is_array($this->services[$id])) {
            $this->services[$id] = $this->instanciateObject($this->services[$id]);
        }
        return $this->services[$id];
    }

    public function setParameter($id, $value)
    {
        $this->params[$id] = $value;
    }

    public function getParameter($id)
    {
        if (!isset($this->params[$id])){
            throw new \InvalidArgumentException("There is no param define with id [$id]");
        }
        return $this->params[$id];
    }

    public function createEmptyList($id)
    {
        $this->lists[$id] = array();
    }

    public function addToList($id, $class, $options = null)
    {
        $this->validateClass($class);
        if (!isset($this->lists[$id])){
            $this->createEmptyList($id);
        }
        $this->lists[$id][] = array($class, $options);
    }

    public function getList($id)
    {
        if (!isset($this->lists[$id])){
            throw new \InvalidArgumentException("There is no list define with id [$id]");
        }
        foreach ($this->lists[$id] as $pos => $object){
            if (is_array($object)) {
                $this->lists[$id][$pos] = $this->instanciateObject($object);
            }
        }
        return $this->lists[$id];
    }

    protected function instanciateObject($objectDefinition)
    {
        list($className, $options) = $objectDefinition;
        $object = new $className($options);
        if ($object instanceof ContextAwareInterface) {
            $object->setContext($this);
        }
        
        return $object;
    }

    protected function validateClass($className)
    {
        if (!class_exists($className)){
            throw new \InvalidArgumentException("The class [$className] does not exist");
        }
    }

    /**
     * Shortcut to retried a service
     * */
    public function get($serviceName)
    {
        return $this->getService($serviceName);
    }

    /**
     * Shortcut to retried a parameter
     * */
    public function getParam($name)
    {
        return $this->getParameter($name);
    }
    
    /**
     * Returns the configured version generator.
     * 
     * @return \Liip\RMT\Version\Generator\GeneratorInterface
     */
    public function getVersionGenerator()
    {
        return $this->get('version-generator');
    }
    
    /**
     * Returns the VCS.
     * 
     * @return \Liip\RMT\VCS\VCSInterface
     */
    public function getVCS()
    {
        return $this->get('vcs'); 
    }
    
    /**
     * Returns the information collector.
     * 
     * @return Information\InformationCollector
     */
    public function getInformationCollector()
    {
        return $this->get('information-collector');
    }
}
