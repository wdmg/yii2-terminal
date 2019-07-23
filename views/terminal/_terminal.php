<?php

use yii\helpers\Html;
use wdmg\terminal\TerminalAsset;
$bundle = TerminalAsset::register($this);
/* @var $this yii\web\View */

$terminalId = rand(1, 999);

?>
<div id="terminalArea-<?= $terminalId; ?>" class="terminal"></div>
<?php $this->registerJs(<<< JS
jQuery(function($, undefined) {
    var terminal = $('#terminalArea-$terminalId');
    terminal.terminal(function(command, term) {
        
        if (command.indexOf('yii') === 0 || command.indexOf('yii') === 3) {
            term.pause();
            $.jrpc('{$rpcRoute}', 'system.describe', [command.replace(/^yii ?/, '')], function(response, status, jqXHR) {
                term.echo(response.result).resume();
            }, function() {
                term.resume();
            });
        } else if (command === 'help') {
            term.echo('Available command`s:');
            term.echo("\tclear\t- to clear the console");
            term.echo('\thelp\t- print this help dialog');
            term.echo('\tyii\t\t- list of yii command`s');
            // term.echo('\tquit\t- to quit from terminal');
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
