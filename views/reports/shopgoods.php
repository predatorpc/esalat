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
    '0' => Yii::t('admin', 'Удален')

];
?>
<div class="orders-index">


    <?php
    //print_r($dataProvider);
    $colorPluginOptions =  [
        'showPalette' => true,
        'showPaletteOnly' => true,
        'showSelectionPalette' => true,
        'showAlpha' => false,
        'allowEmpty' => false,
        'preferredFormat' => 'name',
        'palette' => [
            [
                "white", "black", "grey", "silver", "gold", "brown",
            ],
            [
                "red", "orange", "yellow", "indigo", "maroon", "pink"
            ],
            [
                "blue", "green", "violet", "cyan", "magenta", "purple",
            ],
        ]
    ];

    $gridColumns = [
        [
            'class'=>'kartik\grid\SerialColumn',
            'width'=>'36px',
            'header'=>Yii::t('admin', 'НПП'),
        ],
        [
            'attribute'=>'id',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
        ],
        [
            'attribute'=>'name',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
            'value'=>function ($model, $key, $index, $widget) {
                return Html::a($model->name,['/reports/viewgoods?id='.$model->id]);
            },
            'format'=>'raw',
        ],
        [
            'attribute'=>'phone',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
        ],
        [
            'attribute'=>'description',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'50%',
            'format'=>'raw',
            'mergeHeader'=>true,
        ],
        [
            'attribute'=>'status',
            'hAlign'=>'right',
            'vAlign'=>'middle',
            'width'=>'10%',
            'value'=>function ($model, $key, $index, $widget) {
                if($model->status==1){
                    return Html::tag('span') .Yii::t('admin', 'Активен'). Html::tag('/span');
                }
                else{
                    return Html::tag('span') .Yii::t('admin', 'Удален'). Html::tag('/span');
                }

            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>$itemsStatusDel,
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>Yii::t('admin', 'Активость')],
            'format'=>'raw'
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
