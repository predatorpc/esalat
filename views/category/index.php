<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
$this->title = Yii::t('admin','Управление категориями');

?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="form-group">
        <?=Html::a(Yii::t('admin','Добавить категорию +'), ['create'], ['class' => 'btn btn-primary']); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',

            [
                'attribute'=>'active',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    if($data['active']==1) return Html::tag('p',Yii::t('admin','Активная'));
                    if($data['active']==0) return Html::tag('p',Yii::t('admin','Не активная'));
                },
            ],
//            'parent_id',
//            'googl_id',
            'level',
//            'title',
            [
                'attribute'=>'title',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['title'], "/category/update?id=".$url);
                },
            ],

            //'seo_title',
            // 'seo_description',
            // 'seo_keywords',
             'description:ntext',
            // 'alias',
            // 'sort',

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>Yii::t('admin','Действия'),
                'headerOptions' => ['width' => '80'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-share"></span>',
                            '/category/view?id='.$model->id);
                    },
                    'update' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
                            '/category/update?id='.$model->id);
                    },
//                    'delete' => function ($url,$model) use ($userId) {
//                        if($userId == 10013181)
//                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
//                                '/category/delete?id='.$model->id,
//                                [
//                                    //'class' => 'btn btn-danger',
//                                    'data' => [
//                                        'confirm' => 'Точно удалить?',
//                                        //'method' => 'get',
//                                    ]
//                                ]);
//                        else
//                            return Html::a('');
//                    },
                ],

            ],


        ],
    ]); ?>

</div>
