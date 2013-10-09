<?php

namespace Liip\RMT\Action;

/**
 * Execute any script.
 * 
 * Notice: Append "2>&1" to redirect output.
 */
class ExecuteAction extends BaseAction
{
    protected $options;

    /**
     * Constructor.
     * 
     * @param array $options
     * @throws \LogicException
     */
    public function __construct(array $options)
    {
        if (!array_key_exists('script', $options)) {
            throw new \LogicException('ExcecuteAction requires an option "script" to execute.');
        }
        $this->options = $options;
    }
    
    /**
     * Return the name of the action
     * 
     * @return string
     */
    public function getTitle()
    {
        return 'Execute "' . $this->options['script'] . '"';
    }
    
    /**
     * Execute replacement.
     * 
     * @throws \RuntimeException
     */
    public function execute()
    {
        $returnCode = 0;
        ob_start();
        ob_implicit_flush(false);
        system($this->options['script'], $returnCode);
        $lastLine = ob_get_contents(); 
        ob_end_clean();
        
        if ($returnCode > 0) {
            throw new \RuntimeException('Execution of script "' . $this->options['script'] . '" failed.');
        }
        
        $this->context->get('output')->writeln('<info>OK ' . $lastLine . '</info>');
    }

}