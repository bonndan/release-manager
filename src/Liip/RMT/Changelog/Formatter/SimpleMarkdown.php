<?php

namespace Liip\RMT\Changelog\Formatter;

use Liip\RMT\Changelog\Changelog;

/**
 * Transforms the changelog into simple markdown.
 * 
 */
class SimpleMarkdown
{
    /**
     * changelog
     * 
     * @var \Liip\RMT\Changelog\Changelog
     */
    private $changelog;
    
    public function __construct(Changelog $changelog)
    {
        $this->changelog = $changelog;
    }
    
    public function render($file = "CHANGELOG.md")
    {
        $buffer = '# Changelog' . PHP_EOL;
        $versions = $this->changelog->getVersions();
        
        foreach ($versions as $version) {
            $versionNumber = Changelog::getVersionNumberFromVersion($version);
            $title = Changelog::getTitleFromVersion($version);
            $buffer .= PHP_EOL .'## ' . $versionNumber . ' - ' . $title . PHP_EOL. PHP_EOL;
            
            $commits = Changelog::getCommitsFromVersion($version);
            foreach ($commits as $hash => $message) {
                $buffer .= '* ' . $message . "[$hash]" . PHP_EOL;
            }
        }
        
        file_put_contents($file, $buffer);
    }
}