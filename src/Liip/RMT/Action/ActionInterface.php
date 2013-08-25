<?php

namespace Liip\RMT\Action;

/**
 * Interface for actions.
 * 
 * 
 */
interface ActionInterface
{
    public function getTitle();
    
    public function getInformationRequests();
    
    public function execute();
}