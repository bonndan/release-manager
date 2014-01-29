<?php
namespace Liip\RMT\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Liip\RMT\Information\InformationCollector;
use Liip\RMT\Information\InteractiveQuestion;
use Liip\RMT\Information\InformationRequest;
use Liip\RMT\Context;

/**
 * Main command, used to release a new version
 */
class ReleaseCommand extends BaseCommand
{
    const INCREMENT_MAJOR   = 'major';
    const INCREMENT_MINOR   = 'minor';
    const INCREMENT_PATCH   = 'patch';
    const INCREMENT_BUILD   = 'build';
    
    protected function configure()
    {
        $this->setName('release');
        $this->setDescription('Release a new version of the project');
        $this->setHelp('The <comment>release</comment> interactive task must be used to create a new version of a project');

        $this->loadInformationCollector();

        // Register the command option
        
        foreach ($this->getContext()->getInformationCollector()->getCommandOptions() as $option) {
            $this->getDefinition()->addOption($option);
        }
    }

    protected function loadInformationCollector()
    {
        $ic = new InformationCollector();

        // Add a specific option if it's the first release
        $version = $this->getContext()->getVersionPersister()->getCurrentVersion();
        if ($version->isInitial()) {
            $ic->registerRequest(
                new InformationRequest('confirm-first', array(
                    'description' => 'This is the first release for the current branch',
                    'type' => 'confirmation'
                ))
            );
        }

        // the release task requires the type option
        $ic->registerStandardRequest('type');

        // Register options of all lists (prerequistes and actions)
        foreach (array('prerequisites', 'preReleaseActions', 'postReleaseActions') as $listName){
            foreach ($this->getContext()->getList($listName) as $listItem){
                $ic->registerRequests($listItem->getInformationRequests());
            }
        }

        $this->getContext()->setService('information-collector', $ic);
    }

    // Always executed
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->getContext()->setService('output', $this->output);
        $this->getContext()->getInformationCollector()->handleCommandInput($input);
        
        // Get the current version or generate a new one if the user has confirm that this is required
        try {
            $currentVersion = $this->getContext()->getVersionPersister()->getCurrentVersion();
        }
        catch (\Liip\RMT\Exception\NoReleaseFoundException $e){
            if ($this->getContext()->getInformationCollector()->getValueFor('confirm-first') == false){
                throw $e;
            }
            $currentVersion = \Liip\RMT\Version::createInitialVersion();
        }
        $this->getContext()->setParameter('current-version', $currentVersion);
        

        $this->writeBigTitle('New release (current is ' . $currentVersion . ')');
        $this->executeActionListIfExist('prerequisites');
    }

    // Executed only when we are in interactive mode
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        // Fill up questions
        $infoCollector = $this->getContext()->getInformationCollector();
        if ($infoCollector->hasMissingInformation()){
            $this->writeSmallTitle('Information collect');
            $this->getOutput()->indent();
            foreach($infoCollector->getInteractiveQuestions() as $name => $question) {
                $answer = $this->askQuestion($question);
                $infoCollector->setValueFor($name, $answer);
                $this->writeEmptyLine();
            }
            $this->getOutput()->unIndent();
        }
    }

    // Always executed, but first initialize and interact have already been called
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Generate and save the new version number
        $increment  = $this->getContext()->getInformationCollector()->getValueFor('type');
        $generator = new \Liip\RMT\Version\Generator\SemanticGenerator();
        $newVersion = $generator->generateNextVersion(
            $this->getContext()->getParam('current-version'), $increment
        );
        $this->getContext()->setNewVersion($newVersion);

        $this->executeActionListIfExist('preReleaseActions');

        $this->writeSmallTitle('Release process');
        $this->getOutput()->indent();

        $this->getOutput()->writeln("A new version named [<yellow>$newVersion</yellow>] is going to be released");
        $this->getContext()->getVersionPersister()->save($newVersion);
        $this->getOutput()->writeln("Release: <green>Success</green>");

        $this->getOutput()->unIndent();

        $this->executeActionListIfExist('postReleaseActions');
    }

    protected function executeActionListIfExist($name, $title=null)
    {
        $actions = $this->getContext()->getList($name);
        if (count($actions) > 0) {
            $this->writeSmallTitle($title ?: ucfirst($name));
            $this->getOutput()->indent();
            foreach ($actions as $num => $action){
                $this->write($num++.") ".$action->getTitle().' : ');
                $this->getOutput()->indent();
                $action->execute();
                $this->writeEmptyLine();
                $this->getOutput()->unIndent();
            }
            $this->getOutput()->unIndent();
        }
    }
}

