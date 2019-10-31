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
            $.jrpc(
                '{$rpcRoute}',
                'system.describe',
                [command],
                function(response, status, jqXHR) {
                    term.echo(response.result);
                    term.echo('');
                    term.resume();
                }, function(jqXHR, status) {
                    term.resume();
                }
            );
        } else if (command === '') {
            term.resume();
        }
        //term.echo('');
        
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
    
    if (typeof pipe == 'function') {
        var count = 0;
        terminal.terminal(pipe({
            echo: function(string) {
                return new Promise(function(resolve) {
                    term.echo(string);
                    setTimeout(resolve, 1000);
                });
            },
            read: function() {
                return term.read('').then(function(string) {
                    term.echo('read[' +(++count)+']: ' + string);
                });
            }
        }));
    }
    
    /* For modal`s use only */
    var modal = $('.terminal-modal');
    if (modal.length > 0) {
        
        // jQuery UI Resizable
        modal.find('.modal-dialog').resizable({
            alsoResize: '.terminal',
            resize: function(event, ui) {
                terminal.resize((ui.size.width - 20), ui.size.height);
            }
        });
        
        // jQuery UI Draggable
        modal.find('.modal-dialog').draggable({
            handle: '.modal-header'
        });
        
        modal.on('show.bs.modal', function() {
          $(this).find('.modal-body').css({
            'max-height': '100%'
          });
        });
    }

    
});
JS
); ?>
