<?php
/**
 * Created by PhpStorm.
 * User: Никита
 * Date: 28.02.2017
 * Time: 14:21
 */
$this->title = 'Выбор продуктов по категории';
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\common\models\ModFunctions;
$arCategory = \app\modules\catalog\models\Category::find()->where(['active'=>1])->asArray()->orderBy('parent_id')->All();
$map = array();
$arrayHelper = ArrayHelper::map($arCategory, 'id', 'parent_id');
foreach ($arrayHelper as $id => $id_parent){
    if(empty($id_parent)){
        $map[$id] = array();
    }elseif (isset($map[$id_parent])){
        $map[$id_parent][$id] = array();
    }elseif(isset($arrayHelper[$id_parent])){
        $map[$arrayHelper[$id_parent]][$id_parent][] = $id;
    }
}


$temp = ArrayHelper::map($arCategory, 'id', 'title');
$arCategory = array();
foreach ($map as $key => $value){
    if(!isset($temp[$key])){
        continue;
    }
    $arCategory[$key] = $temp[$key];
    foreach ($value as $key1 => $value1){
        $arCategory[$key1] = '->'.$temp[$key1];
        foreach ($value1 as $key2 => $value2){
            $arCategory[$value2] = '-->'.$temp[$value2];
        }
    }
}
$itemsStatusDel = [
    '1' => Yii::t('admin', 'Активный'),
    '0' => Yii::t('admin', 'Не активный')

];

$itemsShowDel = [
    '1' => Yii::t('admin', 'Показывать'),
    '0' => Yii::t('admin', 'Не показывать')

];
?>
<h2><?=$this->title;?></h2>
<form method="GET" >
    <label>Выберите категорию из списка</label>
    <div class="form-group">
        <?=\yii\helpers\Html::dropDownList('category',$categoty_id,$arCategory);?>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary" />Показать</button>
    </div>
</form>


<?php
if(isset($dataProvider)) {
    $colorPluginOptions = [
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
            'class' => 'kartik\grid\SerialColumn',
            'width' => '36px',
            'header' => Yii::t('admin', 'НПП'),
        ],
        [
            'attribute' => 'id',
            'hAlign' => 'right',
            'vAlign' => 'middle',
            //'width'=>'10%',
        ],
        [
            'attribute' => 'full_name',
            'hAlign' => 'right',
            'vAlign' => 'middle',
            // 'width'=>'10%',
            'header' => Yii::t('admin', 'Название'),
            'value' => function ($data) {
                $good = \app\modules\catalog\models\Goods::find()->select(['name'])->where(['id' => $data->good_id])->asArray()->One();
                    return Html::a($good['name'],
                        'http://www.esalad.ru/product/update?id='.$data->good_id);
            },
            'format' => 'raw',

        ],
        [
            'attribute' => 'price',
            'header' => 'Входящая цена',
            'hAlign' => 'right',
            'vAlign' => 'middle',
            //'width'=>'10%',
        ],
        [
            'attribute' => 'comission',
            'hAlign' => 'right',
            'vAlign' => 'middle',
            //'width'=>'10%',
        ],
        [
            'header' => 'Выходная цена',
            'hAlign' => 'right',
            'vAlign' => 'middle',
            'value' => function($data){
                $finalPrice = $data->price + $data->price/100*$data->comission;
                return ModFunctions::moneyFormat($finalPrice);
            }
        ],
        [
            'header' => 'Скидка',
            'hAlign' => 'right',
            'vAlign' => 'middle',
            'value' => function($data) {
                $arDiscount = ['10' => '3',
                    '20' => '7',
                    '30' => '13',
                    '40' => '15',
                    '50' => '20',
                    '60' => '25',
                    '70' => '30',
                    '80' => '35',
                    '90' => '40',
                    '100' => '45',
                    '110' => '50',
                    '120' => '55'
                ];
                if(isset($arDiscount[ceil($data->comission / 10) * 10])){
                    return $arDiscount[ceil($data->comission / 10) * 10].'%';
                }else{
                    return '0%';
                }
            }
        ],
        [
            'header' => 'Поставщик',
            'hAlign' => 'right',
            'vAlign' => 'middle',
            'value' => function($data){
                return !empty($data->shop->name)?$data->shop->name:'Not set';
            }
            //'width'=>'10%',
        ],
//        [
//            'attribute' => 'description',
//            'hAlign' => 'right',
//            'vAlign' => 'middle',
//            'width' => '50%',
//            'mergeHeader' => true,
//            'value' => function ($data) {
//                $good = \app\modules\catalog\models\Goods::find()->select('description')->where(['id' => $data->good_id])->asArray()->One();
//                return $good['description'];
//            },
//            'format' => 'raw',
//        ],
        [
	    'attribute' => 'show',
	    'width' => '10%',
	    'format' => 'raw',
            'hAlign' => 'right',
            'vAlign' => 'middle',
            'label' => 'Показывать',
            'value' => function($model, $key, $index, $widget){
	        if ($model->show == 1) {
                    return Html::tag('span') . Yii::t('admin', 'Показывать') . Html::tag('/span');
                } else {
                    return Html::tag('span') . Yii::t('admin', 'Не показывать') . Html::tag('/span');
                }
	    },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => $itemsShowDel,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t('admin', 'Показывать')],    
	],
        [
            'attribute' => 'status',
            'hAlign' => 'right',
            'vAlign' => 'middle',
            'width' => '10%',
        
            'format' => 'raw',
                'value' => function ($model, $key, $index, $widget) {
                if ($model->status == 1) {
                    return Html::tag('span') . Yii::t('admin', 'Активен') . Html::tag('/span');
                } else {
                    return Html::tag('span') . Yii::t('admin', 'Не активный') . Html::tag('/span');
                }

            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => $itemsStatusDel,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t('admin', 'Активость')],
            
        ],
    ];


    echo GridView::widget([
        'id' => 'kv-grid-demo',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'pjax' => true, // pjax is set to always true for this demo
        // set your toolbar
        'toolbar' => [
            '{export}',
            '{toggleData}',
        ],
        // set export properties
        'export' => [
            'fontAwesome' => true
        ],
        // parameters from the demo form
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,

        ],
        'persistResize' => false,
        //'exportConfig'=>$exportConfig,
    ]);
}
?>