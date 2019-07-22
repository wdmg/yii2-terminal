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
    terminal.terminal(function(command, term) {
        
        if (command.indexOf('yii') === 0 || command.indexOf('yii') === 3) {
            term.pause();
            $.jrpc('{$rpcRoute}', 'system.describe', [command.replace(/^yii ?/, '')], function(response, status, jqXHR) {
                term.echo(response.result).resume();
            }, function() {
                term.resume();
            });
        } else if (command === 'help') {
            term.echo('Available commands are:');
            term.echo("\tclear\t- to clear the console");
            term.echo('\thelp\t- print this help dialog');
            term.echo('\tyii\t\t- list of yii command`s');
            term.echo('\tquit\t- to quit from terminal');
            term.echo('');
        } else if (command === '') {
            term.resume();
        }
        term.echo('');
        
        $('html, body').animate({
            scrollTop: terminal.height()
        }, 'fast');
        
        $('html').on('keydown', function(e) {
            terminal.click();
        });
        
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
