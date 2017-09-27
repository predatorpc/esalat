<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\catalog\models\codes */

$this->title = $model->code;
$this->params['breadcrumbs'][] = ['label' => 'Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="codes-view">

    <h1>Код: <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Редактировать'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Назад'),'/codes/index?sort=-code', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php //= Html::a('Delete', ['delete', 'id' => $model->id], [           'class' => 'btn btn-danger',            'data' => [                'confirm' => 'Areyou sure you want to delete this item?',                'method' => 'post',            ],        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'type_id',
                'label' => 'User',
                'value' => \app\modules\catalog\models\CodesTypes::find()->where('id = '.$model->type_id)->one()->name,
            ],

            [
                'attribute' => 'user_id',
                'label' => 'User',
                'value' => \app\modules\common\models\User::find()->where('id = '.$model->user_id)->one()->name,
            ],

            'code',
            'key',
            'count',
            [
                'attribute' => 'date_begin',
                'label' => Yii::t('admin', 'Зарегистрирован'),
                'value' =>  date("Y-m-d H:i:s", strtotime($model->date_begin)),//,$data->created_at);
                //'format' => 'text',
            ],
            [
                'attribute' => 'date_end',
                'label' => Yii::t('admin', 'Обновлен'),
                'value' =>  date("Y-m-d H:i:s", strtotime($model->date_end)),//,$data->created_at);
                //'format' => 'text',
            ],
            [
                'attribute' => 'status',
                'value' => $model->status ? Yii::t('admin', 'Активный') : Yii::t('admin', 'Неактивный'),
            ],
        ],
    ]) ?>

</div>
