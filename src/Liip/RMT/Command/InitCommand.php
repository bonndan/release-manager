<?php

namespace Liip\RMT\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Liip\RMT\Information\InformationRequest;
use Liip\RMT\Helpers\JSONHelper;

/**
 * Create json settings file and rmt executable
 */
class InitCommand extends BaseCommand
{
    /**
     * information collector
     * 
     * @var \Liip\RMT\Information\InformationCollector 
     */
    protected $informationCollector;
    protected $executablePath;
    
    /**
     * path to command.php
     * @var string
     */
    protected $commandPath;

    /**
     * path to the composer config file
     * 
     * @var string
     */
    protected $configPath;

    protected function buildPaths()
    {
        $projectDir = $this->getApplication()->getProjectRootDir();
        $this->executablePath = $projectDir . '/RMT';
        $this->configPath = $projectDir . '/composer.json';
        $this->commandPath = realpath(__DIR__ . '/../../../../command.php');

        // If possible try to generate a relative link for the command if RMT is installed inside the project
        if (strpos($this->commandPath, $projectDir) === 0) {
            $this->commandPath = substr($this->commandPath, strlen($projectDir) + 1);
        }
    }

    protected function configure()
    {
        $this->setName('init');
        $this->setDescription('Setup a new project configuration in the current directory');
        $this->setHelp('The <comment>init</comment> interactive task can be used to setup a new project');

        // Create an information collector and configure the different information request
        $this->informationCollector = new \Liip\RMT\Information\InformationCollector();
        $this->informationCollector->registerRequests(array(
            new InformationRequest('vcs', array(
                'description' => 'The VCS system to use',
                'type' => 'choice',
                'choices' => array('git', 'hg', 'none'),
                'choices_shortcuts' => array('g' => 'git', 'h' => 'hg', 'n' => 'none'),
                'default' => 'none'
                )),
            new InformationRequest('persister', array(
                'description' => 'The strategy to use to persist the current version value',
                'type' => 'choice',
                'choices' => array('vcs-tag', 'changelog'),
                'choices_shortcuts' => array('t' => 'vcs-tag', 'c' => 'changelog'),
                'command_argument' => true,
                'interactive' => true
                ))
        ));
        foreach ($this->informationCollector->getCommandOptions() as $option) {
            $this->getDefinition()->addOption($option);
        }
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->informationCollector->handleCommandInput($input);
        $this->writeBigTitle('Welcome to Release Management Tool Initialization');
        $this->writeEmptyLine();

        // Guessing elements path
        $this->buildPaths();

        // Security check
        $helper = new \Liip\RMT\Helpers\ComposerConfig($this->getContext());
        $section = $helper->getRMTConfigSection();
        if ($section !== null) {
            throw new \Exception('A config section "extra/rmt" already exists in composer json.');
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        // Fill up questions
        if ($this->informationCollector->hasMissingInformation()) {
            foreach ($this->informationCollector->getInteractiveQuestions() as $name => $question) {
                $answer = $this->askQuestion($question);
                $this->informationCollector->setValueFor($name, $answer);
                $this->writeEmptyLine();
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create the executable task inside the project home
        $this->getOutput()->writeln("Creation of the new executable <info>{$this->executablePath}</info>");
        file_put_contents($this->executablePath, "#!/usr/bin/env php\n" .
            "<?php define('RMT_ROOT_DIR', __DIR__); ?>\n" .
            "<?php require '{$this->commandPath}'; ?>\n"
        );
        exec('chmod +x RMT');

        // Create the config file
        $composerHelper = new \Liip\RMT\Helpers\ComposerConfig();
        $composerHelper->setComposerFile($this->configPath);
        $composerHelper->addRMTConfigSection(\Liip\RMT\Config::create($this->getConfigData()));
        $this->getOutput()->writeln("Added extra/rmt section to <info>{$this->configPath}</info>");

        // Confirmation
        $this->writeBigTitle('Success, you can start using RMT by calling <info>RMT release</info>');
        $this->writeEmptyLine();
    }

    public function getConfigData()
    {
        $config = array();

        $vcs = $this->informationCollector->getValueFor('vcs');
        if ($vcs !== 'none') {
            $config['vcs'] = $vcs;
        }

        $config['version-persister'] = $this->informationCollector->getValueFor('persister');

        return $config;
    }

}

