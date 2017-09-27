<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Lists */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin','Списки'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lists-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'title',
            'description:ntext',
            'image',
            'show_banners',
            'position',
            'change',
            'list_type',
            'level',
            'date_create',
            'date_update',
            'status',
        ],
    ]) ?>

</div>
