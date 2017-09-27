<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\actions\models\ActionsParams */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Actions Params',
]) . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Actions Params'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="actions-params-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
