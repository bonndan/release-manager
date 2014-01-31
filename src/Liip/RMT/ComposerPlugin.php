<?php
namespace Liip\RMT;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Description of ComposerPlugin
 *
 * @author daniel
 */
class ComposerPlugin implements PluginInterface
{
    private $composer;
    private $io;
    
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        
        return true;
    }

}
