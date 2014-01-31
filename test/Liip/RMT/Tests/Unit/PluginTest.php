<?php
namespace Liip\RMT\Tests;

use Composer\Console\Application;
use Composer\IO\ConsoleIO;
use Composer\IO\IOInterface;
use Liip\RMT\ComposerPlugin;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Attempt to set up a testing environment
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class ComposerPluginTest extends PHPUnit_Framework_TestCase
{
    private $composer;
    private $io;
    
    public function setUp()
    {
        $app = new TestApplication();
        $input = new ArrayInput(array());
        $output = new NullOutput();
        $this->io = new ConsoleIO($input, $output, $app->getDefaultHelperSet());
        $app->setIO($this->io);
        $this->composer = $app->getComposer();
    }
    
    public function testActivate()
    {
        $plugin = new ComposerPlugin();
        $result = $plugin->activate($this->composer, $this->io);
        $this->assertTrue($result);
    }
}

/**
 * Application version that allows plugin testing.
 * 
 * @link https://github.com/composer/composer/issues/2610
 */
class TestApplication extends Application
{
    public function getDefaultHelperSet()
    {
        return parent::getDefaultHelperSet();
    }
    
    public function setIO(IOInterface $io)
    {
        $this->io = $io;
    }
}
