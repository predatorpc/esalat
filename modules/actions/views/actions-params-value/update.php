<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\actions\models\ActionsParamsValue */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Actions Params Value',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Actions Params Values'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="actions-params-value-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
