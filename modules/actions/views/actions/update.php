<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\actions\models\Actions */

$this->title = Yii::t('actions', 'Update {modelClass}', [
    'modelClass' => 'Actions',
]) . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('actions', 'Actions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('actions', 'Update');
?>
<div class="actions-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
