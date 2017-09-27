<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\common\models\User;
use app\modules\managment\models\Shops;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopsCallbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчет о звонках';
$this->params['breadcrumbs'][] = $this->title;

$itemsStatus = [
    '0' => Yii::t('admin','Не активная'),
    '1' => Yii::t('admin','Активная'),
];
?>
<div class="shops-callback-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <?php /* <p>
        <?= Html::a('Create Shops Callback', ['create'], ['class' => 'btn btn-success']) ?>
    </p>*/?>


    <?php
        $gridColumns = [
            [
                'class'=>'kartik\grid\SerialColumn',
                'width'=>'36px',
                'header'=>'',
            ],
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'content' => function($data, $value){
                    if(!empty($data->user_id)){
                        $userName = User::find()->select('name')->where('id = '.$data->user_id)->asArray()->one();
                        return $userName['name'];
                    }
                    else
                        return '';
                }
            ],
            [
                'attribute' => 'shop_id',
                'format' => 'raw',
                'content' => function($data, $value){
                    if(!empty($data->shop_id)){
                        $shop = Shops::find()->select('name')->where('id = '.$data->shop_id)->asArray()->one();
                        return $shop['name'];
                    }
                    else
                        return '';
                }
            ],
            [
                'attribute' => 'action',
            ],
            [
                'attribute' => 'date',
            ],
            [
                'attribute' => 'comment',
            ],
            [
                'attribute' => 'phone',
            ],
            [
                'attribute' => 'contact',
            ],
            [
                'attribute' => 'status',
                'value' => function($data){
                    if($data['status'] == 1)
                        return Yii::t('admin','Активная');
                    else
                        return '<p style="color: #ff0000;">'.Yii::t('admin','Не активная').'</p>';

                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>$itemsStatus,
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('admin','Активность')],
                'format' => 'html',
                'format' => 'html',

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
        'pjax'=>false, // pjax is set to always true for this demo
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
            'before'=>'{pager}',
        ],
        'persistResize'=>false,
        //'exportConfig'=>$exportConfig,
    ]);?>
</div>
