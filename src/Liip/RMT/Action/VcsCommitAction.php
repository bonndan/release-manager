<?php

namespace Liip\RMT\Action;

/**
 * Commit everything
 */
class VcsCommitAction extends BaseAction
{
    public function execute()
    {
        $this->context->getVCS()->saveWorkingCopy(
            'Release of new version '. $this->context->getParam('new-version')
        );
        $this->confirmSuccess();
    }
}

