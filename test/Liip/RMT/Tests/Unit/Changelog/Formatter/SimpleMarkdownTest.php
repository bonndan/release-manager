<?php
namespace Liip\RMT\Tests\Unit\Changelog\Formatter;

use Liip\RMT\Changelog\Changelog;
use Liip\RMT\Changelog\Formatter\SimpleMarkdown;

class SimpleMarkdownTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteFile()
    {
        $changelog = new Changelog(dirname(__DIR__) . '/changelog.xml');
        $formatter = new SimpleMarkdown($changelog);
        
        $targetFile = sys_get_temp_dir() .'/test.md';
        @unlink($targetFile);
        $formatter->render($targetFile);
        $this->assertFileExists($targetFile);
        $this->assertContains('Fixed something', file_get_contents($targetFile));
    }
}