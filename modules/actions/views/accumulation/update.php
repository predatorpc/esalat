<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\actions\models\ActionsAccumulation */

$this->title = 'Update Actions Accumulation: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Actions Accumulations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="actions-accumulation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
