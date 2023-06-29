<?php

namespace wdmg\terminal;

/**
 * Yii2 Terminal
 *
 * @category        Module
 * @version         1.3.0
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-terminal
 * @copyright       Copyright (c) 2019 - 2023 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use wdmg\helpers\ArrayHelper;
use Yii;
use wdmg\base\BaseModule;

/**
 * terminal module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\terminal\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = "terminal/index";

    /**
     * @var string, the name of module
     */
    public $name = "Terminal";

    /**
     * @var string, the description of module
     */
    public $description = "Running console commands from a browser";

    /**
     * @var boolean, the flag to allow CLI
     */
    public $allowCLI = false;

    /**
     * @var array, the list of support CLI commands
     */
    public $supportCLI = [
        'php',
        'mysql',
        'mysqli',
        'psql',
        'curl',
        'wget',

        'apt',
        'apt-get',

        'ping',
        'traceroute',

        'tar', // Short for Tape Archiver is an analogue to zip under Linux.
        'gzip', // Run gzip compression on a file.
        'lzma', // Run LZMA compression on a file.

        'ls', // Show directory contents, lists names of files
        'mkdir', // Creates a directory of the specified name
        'cat', // Display contents of a file
        'cd', // Change directory. Change to certain directory name if provided
        'pwd', // Displays the name of the working directory
        'touch', // Creates a blank file with a specified name
        'less', // View contents of specified file, page by page
        'head', // Displays the first/ last 10 lines of a file
        'tail', // Alias of tail
        'rm', // Removes a specified file. This action is permanent. There is no recycle bin
        'rmdir', // Removes a directory
        'history', // Display a listing of the last commands you've run
        'cp', // Copy specified file to a new named file. Use -r flag to copy a directory
        'mv', // Rename a specified file or directory
        'find', // search files and directories. Can use with wildcards (* ? [ ])
        'quota', // Print the amount of space available and used on all shares for the current user
        'scp', // Secure/ SSH copy. Copies from either the local filesystem to a remote filesystem, or vice versa. This will not work if both arguments are on remote systems. The remote system must be specified as user@remote.fqdn:/where/to/put. The user part is optional and only needed if the remote account name doesn’t match the local one. remote.fqdn is the remote machine, the colon afterwards tells scp that you’re giving it a path, and the path tells it where to place the file


    ];

    /**
     * @var string the module version
     */
    private $version = "1.3.0";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 8;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);

    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($options = null)
    {
        $items = [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/'. $this->id],
            'icon' => 'fa-terminal',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id])
        ];

	    if (!is_null($options)) {

		    if (isset($options['count'])) {
			    $items['label'] .= '<span class="badge badge-default float-right">' . $options['count'] . '</span>';
			    unset($options['count']);
		    }

		    if (is_array($options))
			    $items = ArrayHelper::merge($items, $options);

	    }

	    return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);
    }
}