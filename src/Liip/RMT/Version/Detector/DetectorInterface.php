<?php
namespace Liip\RMT\Version\Detector;

/**
 * Interface for classes which can detect the current version.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
interface DetectorInterface
{
    /**
     * Provides the current version.
     * 
     * @return \Liip\RMT\Version
     */
    public function getCurrentVersion();
}
