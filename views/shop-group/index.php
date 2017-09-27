<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('admin', 'Группы магазинов');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="shops-groups">
    <style>
        table thead,table thead a,thead a:link, thead a:visited{color:#444;}
    </style>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Добавить группу').' +', ['shop-group-create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $groups,
        'filterModel' => $groupsSearch,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            'id',
//            'name',

            [
                'label' => Yii::t('admin', 'Название'),
                'attribute' => 'name',
                'content' => function($data, $model) {

                    return Html::a($data['name'],'/shop-management/shop-group-update?id='.$data['id']);
                }
                ,

            ],


            [
                'label' => Yii::t('admin', 'Магазинов'),
                'content' => function($data, $url,$model) {

                    return \app\modules\managment\models\ShopGroupRelated::find(
                    )->where(['shop_group_id' => $data->id])->count();
                }
                ,

            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=> Yii::t('admin', 'Действия'),
                'headerOptions' => ['width' => '80'],
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
                            '/shop-management/shop-group-update?id='.$model->id);
                    },
                   /* 'delete' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            '/shop-management/shop-group-delete?id='.$model->id,
                            [
                                'data' => [
                                    'confirm' => 'Точно удалить?',
                                    'method' => 'post',
                                ]
                            ]);
                    },*/
                ],
              //  'active',

            ],
        ],
    ]);
    ?>
</div>

