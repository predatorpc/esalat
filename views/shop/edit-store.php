<?php

use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
$this->title = 'Остаток на складах ';
?>
<h2><?=$this->title;?></h2>
<?php
if(isset($arShops)&& count($arShops)>0){
    foreach ($arShops as $key => $shop){
        $shop = \app\modules\managment\models\Shops::find()->where(['id'=>$shop->shop_id])->One();

        if($shop->edit_count_good == 1){
            echo '<a class="btn" href="'.Url::to(['shop/edit-store','shop_id'=>$shop->id]).'">'.$shop->name.'</a> ';
        }

    }
    echo '<hr />';
}
?>

<?php if(isset($dataProvider)) {
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'variation_id',
                'label' => Yii::t('admin', 'Вариация'),
            'content' => function($data)
            {
                $variation = \app\modules\catalog\models\GoodsVariations::find()->where(['id'=>$data->variation_id])->One();
                if($variation){
                    return $variation->full_name;
                }else{
                    return '';
                }

            },
            ],
            [
                'attribute' => 'store_id',
                'label' => Yii::t('admin', 'Магазин'),
                'width' => '30%',
                'content' => function ($data) {
                    $store = \app\modules\managment\models\ShopsStores::find()->where(['id' => $data->store_id])->One();
                    return $store->address;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(\app\modules\managment\models\ShopsStores::find()->where(['shop_id' => $shop_id])->orderBy('name')->asArray()->all(), 'id', 'address'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Склад'],
                'format' => 'raw'
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'count',
                'editableOptions' => [
                    'header' => 'Количество',

                ],
            ],

        ],
    ]);
}
?>