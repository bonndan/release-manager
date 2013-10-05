<?php

namespace Liip\RMT\Version\Persister;

use Liip\RMT\ContextAwareInterface;
use Liip\RMT\Context;

/**
 * Parent class for persisters.
 * 
 */
abstract class AbstractPersister implements ContextAwareInterface
{
    /**
     * the context
     * 
     * @var Context
     */
    protected $context;
    
    /**
     * Inject the context, creates the helper.
     * 
     * @param \Liip\RMT\Context $context
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }
    
    public function getInformationRequests()
    {
        return array();
    }
}