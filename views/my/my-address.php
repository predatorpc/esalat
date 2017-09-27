<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Мои адреса';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="private-room-my-address content my">
    <h1><?= Html::encode($this->title) ?></h1>
    <div style="padding: 21px 5px;"><?=\Yii::t('app','Ваш номер')?> ID: <b><?=Yii::$app->user->id?></b></div>
    <div class="add_address"><?= \app\components\WAddressAddModal::widget();?></div>
    <br>
    <br>

    <?= GridView::widget([
        'dataProvider' => $address,
        'id'=>'my-address',
        'emptyText' => Yii::t('app','Адрес не найден!'),
        'layout'=>"{items}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'city',
            'district',
            [
                'attribute' => 'street',
                'label' => Yii::t('app','Адрес'),
                'value' => function ($data, $url, $model){
                    return $data['street'];
                },
            ],
            'house',
            'room',
            'phone',
            [
                'class' => 'yii\grid\ActionColumn',
                // 'header'=>'Сделай это',
                'headerOptions' => ['width' => '80'],
                'template' => '{address-update} {address-delete}',

                'buttons' => [
//                    'address-update' => function ($url,$model) {
//                        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
//                            'address-update?id='.$model->id);
//                    },
                    'address-delete' => function ($url,$model) {

                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            '/my/address-delete?id='.$model->id,
                            [
                                'class'=>'deleteAddress',
                                'data-pjax' => '',
                                'data' => [
                                    'confirm' => Yii::t('app','Точно удалить?'),
                                ]
                            ]);
                    },
                ],

            ],

        ],
    ]); ?>

</div>
