<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SerchOrders */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Товары');
$this->params['breadcrumbs'][] = $this->title;
$itemsStatusDel = [
    '1' => Yii::t('admin', 'Активный'),
    '0' => Yii::t('admin', 'Отключен')

];


?>
<div class="orders-index">


    <?php
    //print_r($dataProvider);


    $gridColumns = [
        [
            'class'=>'kartik\grid\SerialColumn',
            'width'=>'36px',
            'header'=> Yii::t('admin', 'НПП'),
        ],
        [
            'attribute'=>'id',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
            'mergeHeader'=>true,
            'value'=>function($model){
                return Html::a($model->id, 'http://www.esalad.ru/product/update?id='.$model->good_id);
            },
            'format'=>'html',
        ],
        [
            'attribute'=>'code',
            'header'=>'Артикул',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
            'mergeHeader'=>true,
            'value'=>function ($model, $key, $index, $widget) {
                if(!empty($model->code)){
                    return $model->code;
                }
                return Yii::t('admin', 'Не установлен');
            },
        ],
        [
            'attribute'=>'name',
            'hAlign'=>'left',
            'vAlign'=>'middle',
            'width'=>'50%',
            'value'=>function ($model, $key, $index, $widget) {
                if(empty($model->full_name)){
                    return $model->product->name;
                }
                else{
                    return $model->full_name;
                }
                //return $model->product->name.'/'.$model->full_name;
            },
            'format'=>'raw',
            'mergeHeader'=>true,
        ],
        [
            'attribute'=>'product.type_id',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
            'value'=>function ($model, $key, $index, $widget) {
                $type = \app\modules\catalog\models\GoodsTypes::find()->where(['id'=>$model->product['type_id']])->one();
                return $type->name;
            },
            'format'=>'raw',
            'mergeHeader'=>true,
        ],
        [
            'attribute'=>'price',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
            'value'=>function ($model){
                return number_format($model->price, 2, ',', '');
            },
            'format'=>'raw',
            'mergeHeader'=>true,
        ],
        [
            'attribute'=>'comission',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
            'value'=>function ($model){
                return number_format($model->comission, 2, ',', '');
            },
            'format'=>'raw',
            'mergeHeader'=>true,
        ],
        [
            'header'=> Yii::t('admin', 'Выходная цена'),
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
            'format'=>'raw',
            'value'=>function ($model, $key, $index, $widget) {
                return number_format($model->price * (1+($model->comission/100)), 2, ',', '');
            },
            'mergeHeader'=>true,

        ],
        [
            'header'=> Yii::t('admin', 'Количество'),
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
            'format'=>'raw',
            'value'=>function ($model, $key, $index, $widget) {
                if(!empty($model->count)){
                    return $model->count;
                }
                return 0;
            },
            'mergeHeader'=>true,

        ],
        [
            'attribute'=>'status',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
            'value'=>function ($model, $key, $index, $widget) {
                if($model->status==1){
                    if($model->product->confirm==1){
                        return Html::tag('span') .Yii::t('admin', 'Активен'). Html::tag('/span');
                    }
                    else{
                        return Html::tag('span') .Yii::t('admin', 'На модерации'). Html::tag('/span');
                    }
                }
                else{
                    return Html::tag('span') .Yii::t('admin', 'Отключен'). Html::tag('/span');
                }

            },
            /*
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>$itemsStatusDel,
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>'Активность'],*/
            'format'=>'raw',
            'mergeHeader'=>true,
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
            'heading'=>empty($shop['name'])?'':$shop['name'],

        ],
        'persistResize'=>false,
        //'exportConfig'=>$exportConfig,
    ]);
    ?>


</div>
