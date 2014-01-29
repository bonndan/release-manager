<?php

namespace Liip\RMT;

require_once realpath(__DIR__ . '/../../../') . '/version.php';

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Liip\RMT\Exception\NoConfigurationException;

/**
 * Release Manager application.
 * 
 */
class Application extends BaseApplication
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Creation
        parent::__construct('Release Manager', RMT_VERSION);

        // Change the current directory in favor of the project root folder,
        // this allow to run the task from outside the project like:
        //     $/home/www> myproject/RMT release
        chdir($this->getProjectRootDir());

        // Add all command, in a controlled way and render exception if any
        try {
            // Add the default command
            $this->add($this->createCommand('InitCommand'));
            
            try {
                $config = $this->getConfig();
                $context = Context::create($this);
                
                // Add command that require the config file
                $this->add($this->createCommand('ReleaseCommand'));
                $this->add($this->createCommand('CurrentCommand'));
                $this->add($this->createCommand('ChangesCommand'));
                
                if ($context->getVCS() instanceof VCS\Git) {
                    $this->add($this->createCommand("FlowCommand"));
                }
                
            } catch (NoConfigurationException $exception) {
                echo $exception->getMessage();
            }
        } catch (\Exception $e) {
            $output = new \Liip\RMT\Output\Output();
            $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
            $this->renderException($e, $output);
            exit(1);
        }
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return parent::run($input, new \Liip\RMT\Output\Output());
    }

    public function getProjectRootDir()
    {
        if (defined('RMT_ROOT_DIR')) {
            return RMT_ROOT_DIR;
        } else {
            return getcwd();
        }
    }

    /**
     * Creates a command instance.
     * 
     * @param string $commandClass
     * @return \Liip\RMT\Command\BaseCommand
     */
    protected function createCommand($commandClass)
    {
        $classname = '\Liip\RMT\Command\\' . $commandClass;
        $command = new $classname($commandClass, $this);
        return $command;
    }

    /**
     * Returns the path and name of the composer config file.
     * 
     * @return string
     */
    public function getConfigFilePath()
    {
        return $this->getProjectRootDir() . '/composer.json';
    }

    /**
     * Returns the configuration.
     * 
     * @return object
     * @throws NoConfigurationException
     */
    public function getConfig()
    {
        $helper = new Helpers\ComposerConfig();
        $file   = $this->getProjectRootDir() . '/composer.json';
        $helper->setComposerFile($file);
        $config = $helper->getRMTConfigSection();

        if ($config === null) {
            throw new NoConfigurationException("Impossible to locate the extra/rmt config section in $file. If it's the first time you
                are using this tool, you need to setup your project using the [RMT init] command"
            );
        }

        return $config;
    }

}
