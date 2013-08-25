<?php
/**
 * ContextAwareInterface.php
 * 
 */
namespace Liip\RMT;

use Liip\RMT\Context;

/**
 * Interface for classes which require the context.
 *
 */
interface ContextAwareInterface
{
    /**
     * Inject the context.
     * 
     * @param Context $context
     */
    public function setContext(Context $context);
}

