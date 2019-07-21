<?php
namespace wdmg\terminal;
use yii\web\AssetBundle;

/**
 * TerminalAsset
 *
 * @author Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 */
class TerminalAsset extends AssetBundle
{
    public $sourcePath = '@wdmg/terminal/assets';

    public $css = [
        'terminal.css',
    ];

    public $depends = [
        'wdmg\terminal\JqueryTerminalAsset',
    ];

    public function init()
    {
        parent::init();
    }
}