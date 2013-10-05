<?php

namespace Liip\RMT\Action;

/**
 * Update the version in composer.json
 */
class ComposerUpdateAction extends \Liip\RMT\Action\BaseAction
{
    /**
     * Execute replacement.
     * 
     */
    public function execute()
    {
        $helper = new \Liip\RMT\Helpers\ComposerConfig($this->context);
        $helper->setVersion($this->context->getParam('new-version'));
        $this->confirmSuccess();
    }

}
