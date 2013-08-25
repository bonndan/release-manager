<?php

namespace Liip\RMT\Action;

use Liip\RMT\Information\InformationRequest;

/**
 * Push current branch and tag to version control
 */
class VcsPublishAction extends BaseAction
{
    public function execute()
    {
        if ($this->context->get('information-collector')->getValueFor('confirm-publish') !== 'y'){
            $this->context->get('output')->writeln('<error>requested to be ignored</error>');
            return;
        }

        $this->context->getVCS()->publishChanges();
        $this->context->getVCS()->publishTag(
            $this->context->get('version-persister')->getTagFromVersion(
                $this->context->getParam('new-version')
            )
        );

        $this->confirmSuccess();
    }

    public function getInformationRequests()
    {
        return array(
            new InformationRequest('confirm-publish', array(
                'description' => 'Changes will be published automatically',
                'type' => 'yes-no',
                'default' => 'yes'
            ))
        );
    }
}

