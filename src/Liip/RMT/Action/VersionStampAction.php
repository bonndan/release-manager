<?php
/**
 * VersionStampAction.php
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
namespace Liip\RMT\Action;

/**
 * RMT Action that writes the current version into a file as constant.
 * 
 * The default constant name is APPLICATION_VERSION and the file is version.php.
 * 
 * <code>
 * ...
 * "pre-release-actions": [
 *     {
 *          "name"  : "version-stamp",
 *          "file"  : "config/version.php",
 *          "const" : "MY_APP_VERSION"
 *     },
 *     "vcs-commit"
 * ]
 * </code>
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class VersionStampAction extends BaseAction
{
    /**
     * configurable options.
     * 
     * @var array
     */
    protected $options = array();

    /**
     * Constructor.
     * 
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = array_merge(
            array('file' => 'version.php', 'const' => 'APPLICATION_VERSION'), 
            $options
        );
    }

    /**
     * Writes the current version to the target file.
     * 
     * @return void
     */
    public function execute()
    {
        $version = $this->context->getParameter('new-version');
        if ($version == null) {
            throw new \RuntimeException('Could not determine the new version.');
        }
        
        $const = $this->options['const'];
        $template = "<?php\ndefine('$const', '%s');\n";

        file_put_contents($this->options['file'], sprintf($template, $version));
        $this->confirmSuccess();
    }
}