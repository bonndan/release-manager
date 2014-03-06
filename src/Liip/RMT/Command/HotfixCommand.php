<?php
namespace Liip\RMT\Command;

use Liip\RMT\Action\GitFlowStartHotfixAction;
use Liip\RMT\Exception;
use Liip\RMT\Information\InformationCollector;
use Liip\RMT\Version;
use Liip\RMT\Version\Detector\GitFlowBranch;
use Liip\RMT\Version\Generator\SemanticGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that eases hotfixing with "git flow".
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class HotfixCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('hotfix');
        $this->setDescription('Hotfix with the flow.');
        $this->setHelp('The <comment>hotfix</comment> interactive task must be used with git flow');
    }
    
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $ic = new InformationCollector();
        $ic->registerStandardRequest('type');
        $this->getContext()->setService('information-collector', $ic);
        $this->getContext()->getInformationCollector()->setValueFor('type', 'patch');
        $this->getContext()->setService('information-collector', $ic);
        $this->getContext()->setService('output', $this->output);
        $this->getContext()->getInformationCollector()->handleCommandInput($input);
        
        $currentVersion = $this->getContext()->getVersionDetector()->getCurrentVersion();
        $this->getContext()->setParameter('current-version', $currentVersion);
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        // Fill up questions
        $infoCollector = $this->getContext()->getInformationCollector();
        $currentVersion = $this->getContext()->getParameter('current-version');
        $this->writeBigTitle('RMT hotfix (based on ' . $currentVersion . ')');
       
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
        $detector = new GitFlowBranch($this->getContext()->getVCS(), GitFlowBranch::HOTFIX);
        if ($detector->isInTheFlow()) {
            throw new Exception("Detected a git flow branch. Finish it first.");
        }
        
        // Generate and save the new version number
        $generator = new SemanticGenerator();
        $newVersion = $generator->generateNextVersion(
            $this->getContext()->getParam('current-version'), 
            Version::TYPE_PATCH
        );
        $this->getContext()->setNewVersion($newVersion);

        $action = new GitFlowStartHotfixAction();
        $action->setContext($this->getContext());
        $action->execute();
    }
}
