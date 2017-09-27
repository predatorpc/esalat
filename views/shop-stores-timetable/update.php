<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\managment\models\ShopStoresTimetable */

$this->title = 'Редактировать время работы склада ('.$model->store->AddressStringTitle.')';
$this->params['breadcrumbs'][] = ['label' => 'Время работы склада', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="shop-stores-timetable-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
