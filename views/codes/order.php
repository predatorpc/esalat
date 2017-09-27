<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\catalog\tagssearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Список промокодов');

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tags-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            ['attribute' => 'name',
                'label' => Yii::t('admin', 'Название'),
            ],
            ['attribute' => 'discount',
                'label' => Yii::t('admin', 'Скидка'),
            ],
            ['attribute' => 'fee',
                'label' => Yii::t('admin', 'Fee'),
            ],
            ['attribute' => 'discount_sport',
                'label' => Yii::t('admin', 'Discount sport'),
            ],
            ['attribute' => 'fee_sport',
                'label' => Yii::t('admin', 'Fee sport'),
            ],
            ['attribute' => 'max_sum_fee',
                'label' => Yii::t('admin', 'Max sum of cash back'),
                'value' => function($data){
                    if($data->max_sum_fee == NULL) {
                        return '-';
                    }else{
                        return $data->max_sum_fee;
                    }
                }
            ],
            ['attribute' => 'period_days',
                'label' => Yii::t('admin', 'Период'),
                'value' => function($data){
                    if($data->period_days == NULL) {
                        return '-';
                    }else{
                        return $data->period_days;
                    }
                }
            ],
            ['attribute' => 'show',
                'label' => Yii::t('admin', 'Показывать'),
                'value' => function($data){
                    if($data->show == NULL) {
                        return '-';
                    }else{
                        return $data->show;
                    }
                }
            ],
            ['attribute' => 'status',
                'label' => Yii::t('admin', 'Статус'),
            ],
            ['attribute' => 'shop_id',
                'label' => Yii::t('admin', 'Магазин'),
                'width' => '30%',
                'content' => function($data)
                {   $shop = \app\modules\managment\models\Shops::find()->where(['id'=>$data->shop_id])->asArray()->One();
                    return $shop['name'];
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(\app\modules\managment\models\Shops::find()->where(['status'=>1])->orderBy('name')->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>'Магазин'],
                'format'=>'raw'
                        ],
            ['attribute' => 'money_discount',
                'label' => Yii::t('admin', 'Money Discount'),
                'value' => function($data){
                    if($data->money_discount == NULL) {
                        return '-';
                    }else{
                        return $data->money_discount;
                    }
                }
            ],
        ],
    ]); ?>
</div>
