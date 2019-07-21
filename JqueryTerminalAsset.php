<?php
namespace wdmg\terminal;
use yii\web\AssetBundle;

/**
 * JqueryTerminalAsset
 *
 * @see jQuery Terminal Emulator - https://github.com/jcubic/jquery.terminal
 * @author Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 */
class JqueryTerminalAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery.terminal';

    public $js = [
        YII_ENV_DEV ? 'js/jquery.terminal.js' : 'js/jquery.terminal.min.js',
    ];

    public $css = [
        YII_ENV_DEV ? 'css/jquery.terminal.css' : 'css/jquery.terminal.min.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        parent::init();
    }

}