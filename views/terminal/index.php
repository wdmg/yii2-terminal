<?php

use yii\helpers\Html;
use wdmg\terminal\TerminalAsset;
/* @var $this yii\web\View */

$this->title = Yii::t('app/modules/terminal', 'Terminal');
$this->params['breadcrumbs'][] = $this->title;
$bundle = TerminalAsset::register($this);

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="terminal-index">
    <div id="terminalArea" class="terminal"></div>
</div>

<?php $this->registerJs(<<< JS
jQuery(function($, undefined) {
    var terminal = $('#terminalArea');
    terminal.terminal(function(command) {
        if (command !== '') {
            var result = window.eval(command);
            if (result != undefined) {
                this.echo(String(result));
            }
        }
    }, {
        greetings: '$greetings',
        name: 'yii2-terminal',
        height: 320,
        prompt: '$prompt'
    });
});



JS
); ?>

<?php echo $this->render('../_debug'); ?>
