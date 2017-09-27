<?php

use app\modules\catalog\models\Goods;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\modules\common\models\UserShop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Просмотр брошенных корзин');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logs-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]);

    ?>
    <div class="table-ad+">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",

        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
	    'id',

            [
                'label' => Yii::t('admin', 'Покупатель'),
//                'attribute'=>'user_id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    $result = !empty($data->user_id) ? $data->user->name : '';
                    $result .= !empty($data->user->phone) ? '<br />'.$data->user->phone : '';
                    if(!empty($data->user->staff))
                        $result .= "<br /><span style='color: #ebebf8;    background: #7c7ce2;    border-radius: 3px;    padding: 0px 5px 2px 5px;    margin: 0px 0px 0px 8px;'> Сотрудник</span>";
                    return $result;//!empty($data->user_id) ? $data->user->name : '';
                },
            ],

            [
                'label' => Yii::t('admin', 'Последнее обновление корзины'),
//                'attribute' => 'last_update',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return date('Y-m-d',$data->last_update);
                },
            ],

            [
                'label'=> Yii::t('admin', 'Продукты'),
                'format'=>'html',
                'value' => function ($data, $url, $model) {
                    $result = '
                    <div>';
                    if(!empty($data->products)){
                        foreach ($data->products as $product) {
                            $result .= '
                            <div style="line-height: 50px;"><img src="'.$product->product->imageSimple.'" style="width:50px;margin-right:20px;" /><a href="/catalog/'.Goods::getPath($product->product_id).'">'.$product->product->name.'</a>&nbsp;-&nbsp;'.$product->count.' шт.</div>';
                        }
                    }
                    $result .= '
                    </div>';

                    return $result;
                },
            ],
//
//            [
//                'attribute'=>'action',
//                'format'=>'raw',
//                'value' => function ($data, $url, $model) {
//                    return Html::a($data['action'], "/logs/view?id=".$url);
//                },
//            ],
//
//            [
//                'attribute'=>'shop_id',
//                'format'=>'raw',
//                'value' => function ($data, $url, $model) {
//                    return Html::a($data['shop_id'], "/logs/view?id=".$url);
//                },
//            ],
//
//            [
//                'attribute'=>'store_id',
//                'format'=>'raw',
//                'value' => function ($data, $url, $model) {
//                    return Html::a($data['store_id'], "/logs/view?id=".$url);
//                },
//            ],
//
//            [
//                'attribute'=>'good_id',
//                'format'=>'raw',
//                'value' => function ($data, $url, $model) {
//                    return Html::a($data['good_id'], "/logs/view?id=".$url);
//                },
//            ],
//            [
//                'attribute'=>'variation_id',
//                'format'=>'raw',
//                'value' => function ($data, $url, $model) {
//                    return Html::a($data['variation_id'], "/logs/view?id=".$url);
//                },
//            ],
//            [
//                'attribute'=>'category_id',
//                'format'=>'raw',
//                'value' => function ($data, $url, $model) {
//                    return Html::a($data['category_id'], "/logs/view?id=".$url);
//                },
//            ],
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
            [
                'header'=> Yii::t('admin', 'Действия'),
                'headerOptions' => ['width' => '80'],
                'content' => function ($model){
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        '/reports/basketdelete?id='.$model->id.'&url='.Url::to(),
                        [
                            'data' => [
                                'confirm' => Yii::t('admin', 'Точно удалить?'),
                            ]
                        ]);

                },


            ]

        ],
    ]); ?>
    </div>

</div>

