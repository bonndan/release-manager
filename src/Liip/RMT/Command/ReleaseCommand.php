<?php
namespace Liip\RMT\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Liip\RMT\Changelog\ChangelogManager;
use Liip\RMT\Information\InformationCollector;
use Liip\RMT\Information\InteractiveQuestion;
use Liip\RMT\Information\InformationRequest;
use Liip\RMT\Context;

/**
 * Main command, used to release a new version
 */
class ReleaseCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('release');
        $this->setDescription('Release a new version of the project');
        $this->setHelp('The <comment>release</comment> interactive task must be used to create a new version of a project');

        $this->loadInformationCollector();

        // Register the command option
        foreach ($this->getContext()->get('information-collector')->getCommandOptions() as $option) {
            $this->getDefinition()->addOption($option);
        }
    }

    protected function loadInformationCollector()
    {
        $ic = new InformationCollector();

        // Add a specific option if it's the first release
        try {
            $this->getContext()->get('version-persister')->getCurrentVersion();
        }
        catch (\Liip\RMT\Exception\NoReleaseFoundException $e){
            $ic->registerRequest(
                new InformationRequest('confirm-first', array(
                    'description' => 'This is the first release for the current branch',
                    'type' => 'confirmation'
                ))
            );
        }

        // Register options of the release tasks
        $ic->registerRequests($this->getContext()->get('version-generator')->getInformationRequests());
        $ic->registerRequests($this->getContext()->get('version-persister')->getInformationRequests());

        // Register options of all lists (prerequistes and actions)
        foreach (array('prerequisites', 'pre-release-actions', 'post-release-actions') as $listName){
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
        $this->getContext()->get('information-collector')->handleCommandInput($input);

        $this->writeBigTitle('Welcome to Release Management Tool');

        $this->executeActionListIfExist('prerequisites');
    }

    // Executed only when we are in interactive mode
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        // Fill up questions
        if ($this->getContext()->get('information-collector')->hasMissingInformation()){
            $this->writeSmallTitle('Information collect');
            $this->getOutput()->indent();
            foreach($this->getContext()->get('information-collector')->getInteractiveQuestions() as $name => $question) {
                $answer = $this->askQuestion($question);
                $this->getContext()->get('information-collector')->setValueFor($name, $answer);
                $this->writeEmptyLine();
            }
            $this->getOutput()->unIndent();
        }
    }

    // Always executed, but first initialize and interact have already been called
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the current version or generate a new one if the user has confirm that this is required
        try {
            $currentVersion = $this->getContext()->get('version-persister')->getCurrentVersion();
        }
        catch (\Liip\RMT\Exception\NoReleaseFoundException $e){
            if ($this->getContext()->get('information-collector')->getValueFor('confirm-first') == false){
                throw $e;
            }
            $currentVersion = $this->getContext()->get('version-generator')->getInitialVersion();
        }
        $this->getContext()->setParameter('current-version', $currentVersion);

        // Generate and save the new version number
        $newVersion = $this->getContext()->get('version-generator')->generateNextVersion(
            $this->getContext()->getParam('current-version')
        );
        $this->getContext()->setParameter('new-version', $newVersion);

        $this->executeActionListIfExist('pre-release-actions');

        $this->writeSmallTitle('Release process');
        $this->getOutput()->indent();

        $this->getOutput()->writeln("A new version named [<yellow>$newVersion</yellow>] is going to be released");
        $this->getContext()->get('version-persister')->save($newVersion);
        $this->getOutput()->writeln("Release: <green>Success</green>");

        $this->getOutput()->unIndent();

        $this->executeActionListIfExist('post-release-actions');
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

