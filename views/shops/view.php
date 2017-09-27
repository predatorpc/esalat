<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('admin', 'Магазины'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shops-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Редактировать'), ['/shops/update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Вернутся к списку'), ['/shops', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?php

        //echo $userId;

        if($userId == 10013181) {
            echo Html::a(Yii::t('admin', 'Удалить'), ['/shops/delete', 'id' => $model->id],
                [
                'class' => 'btn btn-danger',
                'data'  => [
                    'confirm' => Yii::t('admin', 'Точно удалить?'),
                   // 'method'  => 'get',
                ],
            ]
            );
        }

        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type_id',
            'name',
            'phone',
            'name_full',
            'contract',
            'description:ntext',
            'comission_id',
            'comission_value',
            [
                'attribute'=>'show',
                'filter'=>[0=> Yii::t('admin', 'Нет'), 1=> Yii::t('admin', 'Да')],
                'format'=>'boolean',
            ],
           'registration',
            [
                'attribute'=>'status',
                'filter'=>[0=> Yii::t('admin', 'Нет'), 1=> Yii::t('admin', 'Да')],
                'format'=>'boolean',
            ],

        ],
    ]) ?>

</div>
