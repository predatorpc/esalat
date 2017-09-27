<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Управление продуктами');
$this->params['breadcrumbs'][] = $this->title;
//print Yii::$app->controller->uniqueId;
//print '<br>';
//print Yii::$app->controller->view->uniqueId;
?>
<style>
    table thead,table thead a,thead a:link, thead a:visited{color:#444;}
</style>

<div class="goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Добавить новый продукт'). ' +', ['/product/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
//            'type_id',
            'name',
            'description:html',
//            'bonus',
//            'new',
//            'sale',
//            'link',
//            'date',
            'date_create',
            'date_update',
//            'position',
            'confirm',
            'status',

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=> Yii::t('admin', 'Действия'),
                'headerOptions' => ['width' => '80'],
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
                            '/product/update?id='.$model->id);
                    },
                    'delete' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            '/product/delete?id='.$model->id,
                            [
                                'data' => [
                                    'confirm' => Yii::t('admin', 'Точно удалить?'),
                                    'method' => 'post',
                                ]
                            ]);
                    },
                ],

            ],
        ],
    ]); ?>

</div>
