<?php

namespace Liip\RMT\Command;

use Liip\RMT\Application;
use Liip\RMT\Context;
use Liip\RMT\ContextAwareInterface;
use Liip\RMT\Information\InteractiveQuestion;
use Liip\RMT\Output\Output;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Wrapper/helper around sf2 command
 */
abstract class BaseCommand extends Command implements ContextAwareInterface
{
    /**
     * console input
     * 
     * @var InputInterface
     */
    protected $input;
    
    /**
     * console output
     * 
     * @var Output 
     */
    protected $output;

    /**
     * the context
     * 
     * @var Context
     */
    protected $context;
    
    /**
     * Constructor requires the application instance.
     * 
     * @param string $name any name
     * @param Application $application
     */
    public function __construct($name, Application $application)
    {
        $this->setApplication($application);
        parent::__construct($name);
    }
    
    public function run(InputInterface $input, OutputInterface $output)
    {
        // Store the input and output for easier usage
        $this->input = $input;
        $this->output = $output;
        parent::run($input, $output);
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }
    
    /**
     * Returns the current context.
     * 
     * @return Context
     */
    protected function getContext()
    {
        if ($this->context === null) {
            $this->context = Context::create($this->getApplication());
        }
        
        return $this->context;
    }
    
    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    protected function writeBigTitle($title)
    {
        $this->writeEmptyLine();
        $formatter = $this->getHelperSet()->get('formatter');
        $this->getOutput()->writeln($formatter->formatBlock($title, 'bg=blue;fg=white', true));
    }

    protected function writeSmallTitle($title)
    {
        $this->writeEmptyLine();
        $formatter = $this->getHelperSet()->get('formatter');
        $this->getOutput()->writeln($formatter->formatBlock($title, 'bg=blue;fg=white'));
        $this->writeEmptyLine();
    }

    protected function writeEmptyLine($repeat=1)
    {
        $this->getOutput()->writeln(array_fill(0,$repeat,''));
    }


    protected function write($text)
    {
        $this->getOutput()->write($text);
    }

    protected function askQuestion(InteractiveQuestion $question) {
        $dialog = $this->getHelperSet()->get('dialog');
        return $dialog->askAndValidate(
            $this->getOutput(),
            $question->getFormatedText(),
            $question->getValidator(),
            false,
            $question->getDefault()
        );
    }
}