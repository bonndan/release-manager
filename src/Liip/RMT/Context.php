<?php

namespace Liip\RMT;

use Liip\RMT\ContextAwareInterface;
use Liip\RMT\Action\ActionInterface;
use Liip\RMT\Version;

/**
 * Application context.
 * 
 * 
 */
class Context
{

    const PARAM_NEW_VERSION = 'new-version';
    const PRERELEASE_LIST = "preReleaseActions";

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
        $helper = new Helpers\ComposerConfig();
        $helper->setComposerFile($rootDir . '/composer.json');
        $config = $helper->getRMTConfigSection();
        $context = new Context();
        $builder = new Helpers\ServiceBuilder($context);

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
            $context->setService(
                    "version-detector", $builder->getService($config->getVersionDetector(), 'versionDetector')
            );

            /*
             * popluate lists
             */
            foreach (array("prerequisites", self::PRERELEASE_LIST, "postReleaseActions") as $listName) {
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
            $builder = new Helpers\ServiceBuilder($this);
            $config = array_merge($options, array('name' => $service));
            $service = $builder->getService($config, '');
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
    private function getService($id)
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

    /**
     * Returns a parameter by name.
     * 
     * @param string $id
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getParameter($id)
    {
        if (!isset($this->params[$id])) {
            throw new \InvalidArgumentException("There is no param defined with id [$id]");
        }
        return $this->params[$id];
    }

    private function createEmptyList($id)
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
            throw new \InvalidArgumentException("There is no list defined with id [$id]");
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
     * Shortcut to retried a parameter
     * */
    public function getParam($name)
    {
        return $this->getParameter($name);
    }

    /**
     * Returns the configured version persister.
     * 
     * @return \Liip\RMT\Version\Persister\PersisterInterface
     */
    public function getVersionPersister()
    {
        return $this->getService('version-persister');
    }

    /**
     * Returns the configured version detector.
     * 
     * @return Version\Detector\DetectorInterface
     * @todo remove persister id, use independend detector
     */
    public function getVersionDetector()
    {
        return $this->getService('version-detector');
    }

    /**
     * Returns the VCS.
     * 
     * @return \Liip\RMT\VCS\VCSInterface
     */
    public function getVCS()
    {
        return $this->getService('vcs');
    }

    /**
     * Returns the information collector.
     * 
     * @return Information\InformationCollector
     */
    public function getInformationCollector()
    {
        return $this->getService('information-collector');
    }

    /**
     * Set the new version.
     * 
     * @param Version $version
     */
    public function setNewVersion(Version $version)
    {
        $this->setParameter(self::PARAM_NEW_VERSION, $version);
    }

    /**
     * Return the version if any.
     * 
     * @return Version|null
     */
    public function getNewVersion()
    {
        return $this->getParameter(self::PARAM_NEW_VERSION);
    }

    /**
     * Returns the output
     * 
     * @return Output\Output
     */
    public function getOutput()
    {
        return $this->getService('output');
    }
}
