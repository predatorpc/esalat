<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin','Управление списками товаров');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lists-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin','Добавить список').' +', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'user_id',
                'label' => Yii::t('admin','Пользователь'),
                'content'=> function ($data, $url){
                    return html::a($data['user_id'],'/list/update?id='.$url);
                }
            ],
            [
                'attribute' => 'title',
                'label' => Yii::t('admin','Название'),
                'content'=> function ($data, $url){
                    return html::a($data['title'],'/list/update?id='.$url);
                }
            ],
            [
                'attribute' => 'description',
                'label' => Yii::t('admin','Описание'),
                'content'=> function ($data, $url){
                    return html::a($data['description'],'/list/update?id='.$url);
                }
            ],
            'image',
            // 'show_banners',
            // 'position',
            // 'change',
             'list_type',
            // 'level',
            // 'date_create',
             'date_update',
             //'status',
            [
                'attribute' => 'status',
                'label' => Yii::t('admin','Статус'),
                'content' => function($data){
                    if($data['status'] == 1)
                        return Yii::t('admin','Активный');
                    else
                        return Yii::t('admin','Не активный');

                }
            ],

          //  ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
