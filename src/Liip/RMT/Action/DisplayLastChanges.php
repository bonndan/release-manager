<?php
namespace Liip\RMT\Action;

use Liip\RMT\Action\BaseAction;

/**
 * Displays the last changes in the output.
 * 
 * 
 */
class DisplayLastChanges extends BaseAction
{
    public function getTitle()
    {
        return "Here is the list of changes you are going to release";
    }

    public function execute()
    {
        try {
            $this->context->getOutput()->writeln('');
            $this->context->getOutput()->writeln(
                $this->context->getVCS()->getAllModificationsSince(
                    $this->context->getVersionPersister()->getCurrentVersion()
                )
            );
        }
        catch (\Exception $e){
            $this->context->getOutput()->writeln('<error>No modification found: '.$e->getMessage().'</error>');
        }
    }
}
