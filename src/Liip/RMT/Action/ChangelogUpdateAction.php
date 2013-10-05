<?php

namespace Liip\RMT\Action;

use Liip\RMT\Changelog\ChangelogManager;

/**
 * Update the changelog file.
 * 
 * The default file name is "CHANGELOG".
 */
class ChangelogUpdateAction extends BaseAction
{
    protected $options;

    public function __construct($options)
    {
        $this->options = array_merge(array(
            'dump-commits' => false,
            'format' => 'simple',
            'file' => 'CHANGELOG'
        ), $options);
    }

    public function execute()
    {
        if ($this->options['dump-commits'] == true) {
            $extraLines = $this->context->getVCS()->getAllModificationsSince(
                $this->context->getVersionPersister()->getCurrentVersion(),
                false
            );
            $this->options['extra-lines'] = $extraLines;
            unset($this->options['dump-commits']);
        }

        $manager = new ChangelogManager($this->options['file'], $this->options['format']);
        $manager->update(
            $this->context->getParam('new-version'),
            $this->context->getInformationCollector()->getValueFor('comment'),
            array_merge(
                array('type' => $this->context->getInformationCollector()->getValueFor('type', null)),
                $this->options
            )
        );
        $this->confirmSuccess();
    }

    public function getInformationRequests()
    {
        return array('comment');
    }
}

