<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;


$this->title = 'Goods Preorders';
$this->params['breadcrumbs'][] = $this->title;

$itemsStatusDel = [
    '1' => 'Активный',
    '0' => 'Удален'

];
$getParams = Yii::$app->request->get('GoodsPreorderSearch');
$flagSetFilterDate = false;
if(isset($getParams) && !empty($getParams['date'])){
    $flagSetFilterDate = true;
}

$flagPerodFilter=false;
$get_date = Yii::$app->request->get('GoodsPreorderSearch');
if(!empty($get_date['dateStart']) && !empty($get_date['dateEnd'])){
    $start = $get_date['dateStart'];
    $end = $get_date['dateEnd'];
    $flagPerodFilter = true;
}
else{
    $start = Date('Y-m-d');
    $end = Date('Y-m-d');
}
//var_dump($flagSetFilterDate);
//var_dump($flagPerodFilter);
?>
<div class="goods-preorder-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <label class="control-label">Период</label>
        <form METHOD="get">
        <?=
        DatePicker::widget([
            'name' => 'GoodsPreorderSearch[dateStart]',
            'value' => $start,
            'type' => DatePicker::TYPE_RANGE,
            'name2' => 'GoodsPreorderSearch[dateEnd]',
            'value2' => $end,
            'language'=>Yii::$app->language,
            'separator'=>'-',
            'convertFormat'=>true,
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-MM-dd',

            ]
        ])
        ?>
        <?= \yii\helpers\Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        </form>
    </div>
    <?php
    $gridColumns = [
        [
            'class'=>'kartik\grid\SerialColumn',
            'width'=>'5%',
            'header'=>'',
        ],
        [
            'attribute'=>'good_variant_id',
            'vAlign'=>'middle',
            'label'=>'Продукт',
            'width'=>'55%',
            'value'=>function ($model, $key, $index, $widget) {
                return Html::a($model->variation->product->name .'. '. $model->variation->getTitleWithPropertiesForCatalog(), '/product/update?id='.$model->variation->product->id, ['_blank']) ;
                //return Html::tag('span') . $model->variation->product->name .'. '. $model->variation->getTitleWithPropertiesForCatalog() . Html::tag('/span');
            },
            'format'=>'raw'
        ],
        [
            'attribute'=>'summ',
            'vAlign'=>'middle',
            'width'=>'10%',
            'label'=>'Количество',
            'value'=>function ($model,  $key, $index, $widget) use ($flagSetFilterDate, $flagPerodFilter){
                if($flagPerodFilter){
                    return $model->summ;
                }
                else {
                    return $model->count;//$flagSetFilterDate?($model->count+1):$model->count;
                }
            },
            'mergeHeader'=>true,
            'format'=>'raw',
        ],
        [
            'attribute'=>'addsumm',
            'vAlign'=>'middle',
            'width'=>'10%',
            'label'=>'Количество',
            'value'=>function ($model,  $key, $index, $widget) use ($flagSetFilterDate, $flagPerodFilter){
                if($flagPerodFilter){
                    return $model->addsumm;
                }
                else {
                    return 1;//$flagSetFilterDate?($model->count+1):$model->count;
                }
            },
            'mergeHeader'=>true,
            'format'=>'raw',
        ],
        /*[
            'attribute'=>'date',
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'label'=>'Дата',
            'width'=>'20%',
            'value'=>function ($model, $key, $index, $widget) use ($flagPerodFilter) {
                if($flagPerodFilter){
                    return '00:00';
                }
                else{
                    if((!empty($model->date)) ){
                        return Date('d.m.Y',strtotime($model->date));
                    }
                    return '00:00';
                }


            },
            'filterType'=>GridView::FILTER_DATE,
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            //'format'=>['date', 'php:Y-m-d H:i:s'],
        ],*/
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
