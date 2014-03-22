<?php
namespace Liip\RMT\Command;

use Liip\RMT\Action\VcsTagAction;
use Liip\RMT\Config\Exception;
use Liip\RMT\Context;
use SplDoublyLinkedList;

/**
 * Base class for "start" and "hotfix" commands.
 *
 * @author @author Daniel Pozzi <bonndan76@googlemail.com>
 */
abstract class FlowBeginCommand extends BaseCommand
{
    /**
     * Checks the given list for instances of VcsTagAction.
     * 
     * @param SplDoublyLinkedList $list
     * @throws Liip\RMT\Config\Exception
     */
    private function findTagAction(SplDoublyLinkedList $list)
    {
        foreach ($list as $action) {
            if ($action instanceof VcsTagAction) {
                throw new Exception('Vcs-Tag actions (i.e. manual tagging) must not be used with git-flow.');
            }
        }
    }
    
    /**
     * Removes vcs-tag actions from pre- and post-release action lists.
     * 
     * 
     */
    protected function assertTaggingIsDisabled()
    {
        $this->findTagAction($this->getContext()->getList(Context::PRERELEASE_LIST));
        $this->findTagAction($this->getContext()->getList(Context::POSTRELEASE_LIST));
    }
}
