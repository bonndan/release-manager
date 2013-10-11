<?php
namespace Liip\RMT\Tests\Unit\Changelog;


class ChangelogUpdateActionTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $file = sys_get_temp_dir() . '/test.xml';
        @unlink($file);
        $options = array('file' => $file);
        $action = new \Liip\RMT\Action\ChangelogUpdateAction($options);
        $context = new \Liip\RMT\Context();
        $vcs = $this->getMock("\Liip\RMT\VCS\VCSInterface");
        $context->setService('vcs', $vcs);
        $persister = $this->getMock("\Liip\RMT\Version\Persister\PersisterInterface");
        $context->setService('version-persister', $persister);
        $context->setParameter('new-version', '1.2.3');
        $collector = $this->getMock("\Liip\RMT\Information\InformationCollector");
        $context->setService('information-collector', $collector);
        $output = $this->getMock("\Liip\RMT\Output\Output");
        $context->setService('output', $output);
        
        $action->setContext($context);
        
        $rawLines = "6e6e8ff removed the changelog manager and the functional test
4ada2d8 changelog persister uses the changelog
27d75d8 added assertion
35e533d changelog can return the current version
8c97f13 added comment
";
        $vcs->expects($this->once())
            ->method('getAllModificationsSince')
            ->will($this->returnValue(explode(PHP_EOL, $rawLines)));
        
        $action->execute();
    }
}
