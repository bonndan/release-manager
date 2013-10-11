<?php

namespace Liip\RMT\Action;

use Liip\RMT\Changelog\Changelog;

/**
 * Renders the changelog file.
 * 
 * The default changelog source name is "changelog.xml", the default
 * target is "changelog.md".
 */
class ChangelogRenderAction extends BaseAction
{
    protected $options;

    public function __construct($options)
    {
        $this->options = array_merge(array(
            'changelog' => 'changelog.xml',
            'file' => 'changelog.md',
        ), $options);
    }

    public function execute()
    {
        $changelog = new Changelog($this->options['changelog']);
        $formatter = new \Liip\RMT\Changelog\Formatter\SimpleMarkdown($changelog);
        $formatter->render($this->options['file']);
        $this->confirmSuccess();
    }
}

