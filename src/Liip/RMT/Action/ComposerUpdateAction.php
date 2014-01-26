<?php

namespace Liip\RMT\Action;

use Liip\RMT\Helpers\ComposerConfig;

/**
 * Update the version in composer.json
 */
class ComposerUpdateAction extends BaseAction
{
    /**
     * Execute replacement.
     * 
     */
    public function execute()
    {
        $helper = new ComposerConfig($this->context);
        $helper->setVersion($this->context->getNewVersion());
        $this->confirmSuccess();
    }

}
