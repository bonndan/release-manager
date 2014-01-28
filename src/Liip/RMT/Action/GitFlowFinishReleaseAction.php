<?php
namespace Liip\RMT\Action;

use Liip\RMT\Action\BaseAction;

/**
 * Finishes the current git flow release.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GitFlowFinishReleaseAction extends BaseAction
{
    public function execute()
    {
        $comment = $this->context->getInformationCollector()->getValueFor('comment');
        $this->context->getVCS()->finishRelease($comment);
        $this->context->getOutput()->writeln('Finish the git flow release.');
    }
}
