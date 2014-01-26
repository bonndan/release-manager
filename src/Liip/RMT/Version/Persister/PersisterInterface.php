<?php

namespace Liip\RMT\Version\Persister;

use Liip\RMT\Exception\NoReleaseFoundException;
use Liip\RMT\Version;

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
     * @return Liip\RMT\Version
     * @throws NoReleaseFoundException
     * */
    public function getCurrentVersion();

    /**
     * Saves the version.
     * 
     * @param Version $version
     */
    public function save(Version $version);

    /**
     * @return array
     */
    public function getInformationRequests();
}
