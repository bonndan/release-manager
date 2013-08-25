<?php

namespace Liip\RMT\Prerequisite;

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
            $this->context->get('output')->writeln('');
            $this->context->get('output')->writeln(
                $this->context->get('vcs')->getAllModificationsSince(
                    $this->context->get('version-persister')->getCurrentVersionTag()
                )
            );
        }
        catch (\Exception $e){
            $this->context->get('output')->writeln('<error>No modification found: '.$e->getMessage().'</error>');
        }
    }
}