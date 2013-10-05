<?php

namespace Liip\RMT\Version\Persister;

use Liip\RMT\Exception\NoReleaseFoundException;

/**
 * Interface to version persister implementations.
 * 
 * The persisters must be able to set and to return the version number.
 */
interface PersisterInterface
{
    /**
     * Return the current release name
     *
     * @return mixed The current release number
     * @throws NoReleaseFoundException
     * */
    public function getCurrentVersion();

    /**
     * Saves the version number.
     * 
     * @param string $versionNumber
     */
    public function save($versionNumber);

    /**
     * @return array
     */
    public function getInformationRequests();
}
