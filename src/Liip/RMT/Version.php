<?php
namespace Liip\RMT;

use vierbergenlars\SemVer\version as SemVerVersion;
use vierbergenlars\SemVer\SemVerException;

/**
 * Class representing a semantic version.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Version extends SemVerVersion 
{
    /**
     * @var string
     */
    const INITIAL = '0.0.0';

    /**
     * Factory method to create an initial version.
     * 
     * @return \Liip\RMT\Version
     */
    public static function createInitialVersion() 
    {
        return new static(self::INITIAL);
    }

    /**
     * Returns true if the version number is 0.0.0
     * 
     * @return boolean
     */
    public function isInitial()
    {
        return $this->__toString() == self::INITIAL;
    }

    /**
     * Increment the version number
     * @param string $what One of 'major', 'minor', 'patch' or 'build'
     * @return Version
     * @throws SemVerException When an invalid increment value is given
     */
    public function inc($what) 
    {
        return new static(parent::inc($what)->__toString());
    }

}
