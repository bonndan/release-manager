<?php
/**
 * ChangeLogFormatterInterface.php
 * 
 */
namespace Liip\RMT\Changelog\Formatter;

/**
 * Description
 *
 */
interface ChangeLogFormatterInterface
{
    /**
     * Update the existing line of the changelog.
     * 
     * @param string $lines
     * @param string $version
     * @param string $comment
     * @param array  $options
     */
    public function updateExistingLines($lines, $version, $comment, array $options = null);
}

