<?php
namespace Liip\RMT;

use vierbergenlars\SemVer\version as SemVerVersion;

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
        return new Version(self::INITIAL);
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
}
