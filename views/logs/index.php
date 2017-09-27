<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\models\UserShop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Просмотр логов');
$this->params['breadcrumbs'][] = $this->title;

//$basket = \app\modules\basket\models\BasketLg::find()->where(['id' => intval('114781'),'status' => 0])->all();
//$basket = $basket[0];
//\app\modules\common\models\Zloradnij::print_arr($basket);
?>
<div class="logs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);

    ?>

    <p>
        <?php if($userId == 100131810){echo Html::a(Yii::t('admin', 'Добавить запись'), ['create'], ['class' => 'btn btn-success']); }?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

     /*       [
                'attribute'=>'id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['id'], "/shops/logs/view?id=".$url);

                },
            ],    */
            [
                'attribute'=>'time',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['time'], "/logs/view?id=".$url);
                },
            ],

            [
                'label' => 'Name',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    $userLevel = UserShop::find()
                        ->where(['id' => $data['user_id']])->one();
                    return html::encode($userLevel['name']); //Html::a($userLevel['name'], "/shops/logs/view?id=".$url);

                },
            ],

            [
                'attribute'=>'user_id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['user_id'], "/logs/view?id=".$url);
                },
            ],

            [
                'attribute'=>'action',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['action'], "/logs/view?id=".$url);
                },
            ],

            [
                'attribute'=>'shop_id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['shop_id'], "/logs/view?id=".$url);
                },
            ],

            [
                'attribute'=>'store_id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['store_id'], "/logs/view?id=".$url);
                },
            ],

            [
                'attribute'=>'good_id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['good_id'], "/logs/view?id=".$url);
                },
            ],
            [
                'attribute'=>'variation_id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['variation_id'], "/logs/view?id=".$url);
                },
            ],
            [
                'attribute'=>'category_id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['category_id'], "/logs/view?id=".$url);
                },
            ],
            // 'sql:ntext',

         /*   [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'headerOptions' => ['width' => '80'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-share"></span>',
                            'logs/view?id='.$model->id);
                    },
                    'update' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
                            'logs/update?id='.$model->id);
                    },
                    'delete' => function ($url,$model) use ($userId) {
                        if($userId == 10013181)
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                '/logs/delete?id='.$model->id,
                                [
                                    //'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Точно удалить?',
                                        //'method' => 'get',
                                    ]
                                ]);
                        else
                            return Html::a('');
                    },
                ],

            ],*/

        ],
    ]); ?>

</div>

