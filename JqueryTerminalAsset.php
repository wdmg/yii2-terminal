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
    public $sourcePath = '@bower';

    public $js = YII_ENV_DEV ? [
        ['jquery.terminal/js/jquery.terminal.js', 'position' => \yii\web\View::POS_HEAD],
        ['jquery.terminal/js/pipe.js', 'position' => \yii\web\View::POS_HEAD],
        ['jquery-ui/jquery-ui.js', 'position' => \yii\web\View::POS_HEAD]
    ] : [
        ['jquery.terminal/js/jquery.terminal.min.js', 'position' => \yii\web\View::POS_HEAD],
        ['jquery.terminal/js/pipe.js', 'position' => \yii\web\View::POS_HEAD],
        ['jquery-ui/jquery-ui.min.js', 'position' => \yii\web\View::POS_HEAD]
    ];

    public $css = [
        YII_ENV_DEV ? 'jquery.terminal/css/jquery.terminal.css' : 'jquery.terminal/css/jquery.terminal.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        parent::init();
    }

}