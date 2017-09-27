<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\managment\models\ShopStoresTimetable */

$this->title = 'Добавить время работы для склада ('.$model->store->AddressStringTitle.')';
$this->params['breadcrumbs'][] = ['label' => 'Время работы склада', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-stores-timetable-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
