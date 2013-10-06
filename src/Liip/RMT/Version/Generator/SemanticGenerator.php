<?php

namespace Liip\RMT\Version\Generator;

use vierbergenlars\SemVer\version as Version;
use vierbergenlars\SemVer\SemVerException;

/**
 * Generator based on the Semantic Versioning defined by Tom Preston-Werner
 * Description available here: http://semver.org/
 */
class SemanticGenerator
{
    /**
     * {@inheritDoc}
     * @throws \InvalidArgumentException
     */
    public function generateNextVersion($currentVersion, $increment)
    {
        $version = new Version($currentVersion);
        
        try {
            return $version->inc($increment);
        } catch (SemVerException $exception) {
            throw new \InvalidArgumentException(
                'The option [type] must be one of: {patch, minor, major}, "' . $increment.'" given.',
                500,
                $exception
            );
        }
    }

    public function getInformationRequests()
    {
        return array('type');
    }

    /**
     * Returns 0.0.0
     * 
     * @return string
     */
    public function getInitialVersion()
    {
        return '0.0.0';
    }
}
