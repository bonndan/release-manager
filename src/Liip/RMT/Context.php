<?php

namespace Liip\RMT;

use Liip\RMT\ContextAwareInterface;
use Liip\RMT\Action\ActionInterface;

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
        $rootDir = $application->getProjectRootDir();
        $helper  = new Helpers\ComposerConfig();
        $helper->setComposerFile($rootDir . '/composer.json');
        $config  = $helper->getRMTConfigSection();
        $context = new Context();
        $builder = new Helpers\ServiceBuilder($context);

        /*
         * Populate the context the version generator
         */
        $context->setService("version-generator", new \Liip\RMT\Version\Generator\SemanticGenerator());
        
        /*
         * The following services are config-dependent
         */
        if ($config !== null) {
            if ($config->getVcs()) {
                $context->setService('vcs', $builder->getService($config->getVcs(), 'vcs'));
            }
            
            // Store the config for latter usage
            $context->setParameter('config', $config);
            /*
             * populate version persister
             */
            $context->setService(
                "version-persister", $builder->getService($config->getVersionPersister(), 'versionPersister')
            );
            
            /*
             * popluate lists
             */
            foreach (array("prerequisites", "preReleaseActions", "postReleaseActions") as $listName) {
                $context->createEmptyList($listName);
                foreach ($config->$listName as $service) {
                    $context->addToList($listName, $builder->getService($service, $listName));
                }
            }
        }



        // Provide the root dir as a context parameter
        $context->setParameter('project-root', $rootDir);
        return $context;
    }

    /**
     * Inject a service.
     * 
     * @param string $id
     * @param mixed  $service
     * @param array  $options
     * @throws \InvalidArgumentException
     */
    public function setService($id, $service, array $options = array())
    {
        if (is_string($service)) {
            $builder  = new Helpers\ServiceBuilder($this);
            $config   = array_merge($options, array('name' => $service));
            $service  = $builder->getService($config, '');
        }

        if (!is_object($service)) {
            throw new \InvalidArgumentException("setService() only accept an object or a valid class name");
        }
        
        $this->services[$id] = $service;
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
        if (!isset($this->services[$id])) {
            throw new \InvalidArgumentException("There is no service defined with id [$id]");
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
        if (!isset($this->params[$id])) {
            throw new \InvalidArgumentException("There is no param defined with id [$id]");
        }
        return $this->params[$id];
    }

    public function createEmptyList($id)
    {
        $this->lists[$id] = array();
    }

    /**
     * Adds an action to an action list.
     * 
     * @param string $id
     * @param \Liip\RMT\Action\ActionInterface $action
     */
    public function addToList($id, ActionInterface $action)
    {
        if (!isset($this->lists[$id])) {
            $this->createEmptyList($id);
        }
        $this->lists[$id][] = $action;
    }

    /**
     * Returns a collection of actions.
     * 
     * @param string $id
     * @return \Liip\RMT\Action\ActionInterface[]
     * @throws \InvalidArgumentException
     */
    public function getList($id)
    {
        if (!isset($this->lists[$id])) {
            throw new \InvalidArgumentException("There is no list define with id [$id]");
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
     * Returns the semantic version generator.
     * 
     * @return \Liip\RMT\Version\Generator\SemanticGenerator
     */
    public function getVersionGenerator()
    {
        return $this->get('version-generator');
    }
    
    /**
     * Returns the configured version persister.
     * 
     * @return \Liip\RMT\Version\Persister\PersisterInterface
     */
    public function getVersionPersister()
    {
        return $this->get('version-persister');
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
