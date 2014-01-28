<?php
namespace Liip\RMT\Version\Persister;

use Liip\RMT\Version;

/**
 * Interface to version persister implementations.
 * 
 * The persisters must be able to set the version number.
 */
interface PersisterInterface
{
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
