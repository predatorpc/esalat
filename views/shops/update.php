<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Shops */

$this->title =  Yii::t('admin', 'Редактировать магазин') . ':  ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Магазины'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Редактирование');
?>
<div class="shops-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'id' => $model->id,
        'create' => false,
        'stringHash' => $stringHash,//$error,
    ]) ?>

</div>
