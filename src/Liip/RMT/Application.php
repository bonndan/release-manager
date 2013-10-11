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
                $this->getConfig();
                // Add command that require the config file
                $this->add($this->createCommand('ReleaseCommand'));
                $this->add($this->createCommand('CurrentCommand'));
                $this->add($this->createCommand('ChangesCommand'));
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

    /**
     * @inheritdoc
     */
    public function asText($namespace = null)
    {
        $messages = array();

        // Title
        $title = 'RMT ' . $this->getLongVersion();
        $messages[] = '';
        $messages[] = $title;
        $messages[] = str_pad('', 41, '-'); // strlen is not working here...
        $messages[] = '';

        // Usage
        $messages[] = '<comment>Usage:</comment>';
        $messages[] = '  RMT command [arguments] [options]';
        $messages[] = '';

        // Commands
        $messages[] = '<comment>Available commands:</comment>';
        $commands = $this->all();
        $width = 0;
        foreach ($commands as $command) {
            $width = strlen($command->getName()) > $width ? strlen($command->getName()) : $width;
        }
        $width += 2;
        foreach ($commands as $name => $command) {
            if (in_array($name, array('list', 'help'))) {
                continue;
            }
            $messages[] = sprintf("  <info>%-${width}s</info> %s", $name, $command->getDescription());
        }
        $messages[] = '';

        // Options
        $messages[] = '<comment>Common options:</comment>';
        foreach ($this->getDefinition()->getOptions() as $option) {
            if (in_array($option->getName(), array('help', 'ansi', 'no-ansi', 'no-interaction', 'version'))) {
                continue;
            }
            $messages[] = sprintf('  %-29s %s %s', '<info>--' . $option->getName() . '</info>', $option->getShortcut() ? '<info>-' . $option->getShortcut() . '</info>' : '  ', $option->getDescription()
            );
        }
        $messages[] = '';

        // Help
        $messages[] = '<comment>Help:</comment>';
        $messages[] = '   To get more information about a given command, you can use the help option:';
        $messages[] = sprintf('     %-26s %s %s', '<info>--help</info>', '<info>-h</info>', 'Provide help for the given command');
        $messages[] = '';

        return implode(PHP_EOL, $messages);
    }

}
