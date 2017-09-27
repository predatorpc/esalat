<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\catalog\models\Sticker */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Стикеры', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sticker-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            //'id',
            'name',
            [
                'attribute' => 'status',
                'value' => function($model){
                    if($model->status == 1){
                        return 'Активно';
                    }else{
                        return 'Не активно';
                    }
                }
            ],
        ],
    ]) ?>

</div>
