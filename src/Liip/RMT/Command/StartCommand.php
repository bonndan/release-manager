<?php
namespace Liip\RMT\Command;

use Liip\RMT\Action\GitFlowStartReleaseAction;
use Liip\RMT\Exception;
use Liip\RMT\Version\Detector\GitFlowReleaseBranch;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Liip\RMT\Information\InformationCollector;

/**
 * Command that eases releasing with "git flow".
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class StartCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('start');
        $this->setDescription('Release with the flow.');
        $this->setHelp('The <comment>start</comment> interactive task must be used with git flow');
    }
    
    // Always executed
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $ic = new InformationCollector();
        $ic->registerStandardRequest('type');
        $this->getContext()->setService('information-collector', $ic);
        $this->getContext()->setService('output', $this->output);
        $this->getContext()->getInformationCollector()->handleCommandInput($input);
        
        $currentVersion = $this->getContext()->getVersionDetector()->getCurrentVersion();
        $this->getContext()->setParameter('current-version', $currentVersion);
    }
    
    // Executed only when we are in interactive mode
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        // Fill up questions
        $infoCollector = $this->getContext()->getInformationCollector();
        $currentVersion = $this->getContext()->getParameter('current-version');
        $this->writeBigTitle('RMT start (based on ' . $currentVersion . ')');
       
        if ($infoCollector->hasMissingInformation()){
            $this->writeSmallTitle('What type of version increment brings this release?');
            $this->getOutput()->indent();
            foreach($infoCollector->getInteractiveQuestions() as $name => $question) {
                $answer = $this->askQuestion($question);
                $infoCollector->setValueFor($name, $answer);
                $this->writeEmptyLine();
            }
            $this->getOutput()->unIndent();
        }
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $detector = new GitFlowReleaseBranch($this->getContext()->getVCS());
        try {
            $detector->getCurrentVersion();
        } catch (Exception $ex) {
            
            // Generate and save the new version number
            $increment  = $this->getContext()->getInformationCollector()->getValueFor('type');
            $generator = new \Liip\RMT\Version\Generator\SemanticGenerator();
            $newVersion = $generator->generateNextVersion(
                $this->getContext()->getParam('current-version'), $increment
            );
            $this->getContext()->setNewVersion($newVersion);
        
            $action = new GitFlowStartReleaseAction();
            $action->setContext($this->getContext());
            $action->execute();
            return;
        }
        
        throw new Exception("Detected a git flow release branch. Finish the current release first.");
    }
}