<?php

use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = Yii::t('app/modules/terminal', 'Terminal');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="terminal-index">
    <?= $this->render('_terminal', [
        'module' => $module,
        'greetings' => $greetings,
        'rpcRoute' => $rpcRoute,
        'prompt' => $prompt
    ]); ?>
</div>

<?php echo $this->render('../_debug'); ?>
