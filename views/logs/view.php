<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Logs */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Логи БД'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logs-view">

    <h1><?= Html::encode('ID: '.$model->id) ?></h1>

    <p>
        <?= Html::a('Вернутся назад', ['index'], ['class' => 'btn btn-primary']) ?>
        <?php // Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php /* Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'time',
            'user_id',
            'action',
            'shop_id',
            'store_id',
            'good_id',
            'variation_id',
            'category_id',
            'sql:ntext',
        ],
    ]) ?>

</div>
