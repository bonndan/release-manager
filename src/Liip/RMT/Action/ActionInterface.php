<?php

namespace Liip\RMT\Action;

/**
 * Interface for actions.
 * 
 * 
 */
interface ActionInterface
{
    /**
     * Returns the title of the action
     * 
     * @return string
     */
    public function getTitle();
    
    /**
     * Returns the required info requests.
     * 
     * @return string[]
     */
    public function getInformationRequests();
    
    /**
     * Execute the action
     * 
     * @return void
     */
    public function execute();
}