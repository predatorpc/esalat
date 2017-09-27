<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pages-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'url:url',
            'name',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'status',
             //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>Yii::t('admin', 'Действия'),
                'headerOptions' => ['width' => '80'],
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-share" title="'.Yii::t('admin','Смотреть').'"></span>',
                            '/category/view-page?id='.$model->id);
                    },
                    'update' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-edit" title="'.Yii::t('admin','Редактировать').'"></span>', '/category/update-page?id='.$model->id);
                    },
                ],

            ],
        ],
    ]); ?>
</div>