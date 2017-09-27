<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsersCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Зачисления в кредит');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-credits-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Добавить пользователя'), ['create'], ['class' => 'btn btn-success']) ?>
        <?php
            if((Yii::$app->user->id ==10001482) || (Yii::$app->user->can('GodMode'))){
                echo Html::a(Yii::t('admin', 'Зачислить всем'), ['create-all'], ['class' => 'btn btn-primary']);
            }
        ?>
        <?= Html::a(Yii::t('admin', 'Назад к списку'), ['/users-credits'], ['class' => 'btn btn-primary']) ?>
    </p>
<?php
        $gridColumns = [
            /*[
                'class'=>'kartik\grid\SerialColumn',
                'width'=>'36px',
                'header'=>'',
            ],*/
            [
                'attribute'=>'user_id',
                'vAlign'=>'middle',
                'width'=>'9%',
                'format'=>'raw',
            ],
            [
                'attribute'=>'userName',
                'vAlign'=>'middle',
                'width'=>'180px',
                'value'=>function ($model){
                    return $model->user['name'];
                },
                'format'=>'raw'
            ],
            [
                'attribute'=>'amount',
                'vAlign'=>'middle',
                'width'=>'180px',
                'format'=>'raw'
            ],
            [
                'attribute'=>'status',
                'vAlign'=>'middle',
                'width'=>'180px',
                'label' =>Yii::t('admin', 'Статус'),
                'value'=>function($data){
                    if($data['status'] == 1)
                        return Yii::t('admin','Активный');
                    else
                        return '<p style="color: #ff0000;">'.Yii::t('admin','Не активный').'</p>';

                },
                'format'=>'html'
            ],
            [
                'class'=>'kartik\grid\ActionColumn',
                'dropdownOptions'=>['class'=>'pull-right'],/*
                'urlCreator'=>function($action, $model, $key, $index) {
                    if($action=='delete'){
                        return  '/admin/delete?id='.$key;
                    }
                    else{//if($action=='update'){
                        return  '/admin/update?id='.$key;
                    }
                },*/
                'viewOptions' => ['title'=>'Просомотр', 'data-toggle'=>'tooltip'],
                'updateOptions'=>['title'=>'Внести изменения', 'data-toggle'=>'tooltip'],
                'deleteOptions'=>['title'=>'Удалить', 'data-toggle'=>'tooltip'],
                'headerOptions'=>['class'=>'kartik-sheet-style'],
            ],
        ];

        echo GridView::widget([
            'id' => 'kv-grid-demo',
            'dataProvider'=>$dataProvider,
            'filterModel'=>$searchModel,
            'columns'=>$gridColumns,
            'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
            'headerRowOptions'=>['class'=>'kartik-sheet-style'],
            'filterRowOptions'=>['class'=>'kartik-sheet-style'],
            'pjax'=>true, // pjax is set to always true for this demo
            // set your toolbar
            'toolbar'=> [
                '{export}',
                '{toggleData}',
            ],
            // set export properties
            'export'=>[
                'fontAwesome'=>true
            ],
            // parameters from the demo form
            'panel'=>[
                'type'=>GridView::TYPE_PRIMARY,

            ],
            'persistResize'=>false,
            //'exportConfig'=>$exportConfig,
        ]);
?>
</div>
