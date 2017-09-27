<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\pages\models\Pages */

$this->title = 'Update Pages: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin','Редактировать');
?>
<div class="pages-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-page', [
        'model' => $model,
    ]) ?>

</div>