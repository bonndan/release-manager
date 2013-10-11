<?php

namespace Liip\RMT\Action;

use Liip\RMT\Changelog\Changelog;

/**
 * Update the changelog file.
 * 
 * The default file name is "changelog.xml".
 */
class ChangelogRenderAction extends BaseAction
{
    protected $options;

    public function __construct($options)
    {
        $this->options = array_merge(array(
            'changelog' => 'changelog.xml',
            'file' => 'CHANGELOG',
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

