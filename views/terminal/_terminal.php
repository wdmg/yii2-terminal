<?php

use yii\helpers\Html;
use wdmg\terminal\TerminalAsset;
$bundle = TerminalAsset::register($this);
/* @var $this yii\web\View */

$terminalId = rand(1, 999);

?>
<div id="terminalArea-<?= $terminalId; ?>" class="terminal"></div>
<?php $this->registerJs(<<< JS

$(function() {
    
    var terminal = $('#terminalArea-$terminalId');
    var prompt = '$prompt';
    
    function getRandomInt(max) {
        return Math.floor(Math.random() * Math.floor(max));
    }
    
    function getPrompt() {
        $.ajax({
            url: '{$promptRoute}' + '?_=' + getRandomInt(9999999),
            dataType: 'json',
            success: function(response, status, jqXHR) {
                prompt = response.result;
                terminal.set_prompt(prompt);
                terminal.resume();
            },
            error: function(status, jqXHR) {
                terminal.set_prompt(prompt);
                terminal.resume();
            }
        });
    }
    
    terminal.terminal(function(command, term) {
        
        if (command === 'help') {
            term.echo('Available command`s:');
            term.echo("\tclear\t\t- to clear the console");
            term.echo('\thelp\t\t- print this help dialog');
            term.echo('\tyii help\t- list of yii command`s');
            term.echo('\tphp\t\t\t- run PHP CLI command`s');
            term.echo('\tcurl\t\t- run Curl CLI command`s');
            // term.echo('\tquit\t\t- to quit from terminal');
            term.echo('');
        } else if (command.length >= 2) {
            term.pause();
            
            if (command.indexOf('php yii') >= 0)
                command = command.replace('php yii', 'yii');
            
            $.jrpc(
                '{$rpcRoute}',
                'system.describe',
                [command],
                function(response, status, jqXHR) {
                    term.echo(response.result);
                    getPrompt();
                }, function(jqXHR, status) {
                    getPrompt();
                }
            );
        } else if (command === '') {
            term.resume();
        }
        
    }, {
        greetings: '$greetings',
        name: 'yii2-terminal',
        height: 320,
        prompt: prompt
    });
    
    $('html').on('keydown', function(e) {
        terminal.click();
    });
    
    $('#terminalModal').find('.modal-body').css({
        'max-height': '100%'
    });
    
    // Wait to load js assets
    setTimeout(function() {
        
        // jQuery UI Resizable
        $('#terminalModal').find('.modal-dialog').resizable({
            alsoResize: '.terminal',
            resize: function(event, ui) {
                terminal.resize((ui.size.width - 22), ui.size.height);
            }
        });
        
        // jQuery UI Draggable
        $('#terminalModal').find('.modal-dialog').draggable({
            handle: '.modal-header'
        });
        
    }, 2000);
    
});

JS
); ?>
