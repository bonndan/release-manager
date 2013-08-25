<?php
namespace Liip\RMT\Action;

/**
 * Create a tag with the new version number
 */
class VcsTagAction extends BaseAction
{
    public function execute()
    {
        $this->context->getVCS()->createTag(
            $this->context->getVCS()->getTagFromVersion(
                $this->context->getParam('new-version')
            )
        );
        $this->confirmSuccess();
    }
}

