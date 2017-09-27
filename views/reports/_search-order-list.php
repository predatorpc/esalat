<?php
use kartik\widgets\Select2;
use kartik\widgets\DatePicker;

$orderItemStatusList = \yii\helpers\ArrayHelper::map(\app\modules\shop\models\OrdersStatus::find()->where(['status' => 1])->all(),'id','name');
$orderItemStatusList[999] = Yii::t('admin', 'Не обработан');
$orderItemStatusList[0] = Yii::t('admin', 'Отменен');
$orderItemStatusList['NO'] = Yii::t('admin', 'Не выдан');

$orderTypeList = [
    1 => 'Сайт',
    2 => 'Терминалы',
];

$userType = [
    0 => 'Клиенты',
    1 => 'Сотрудники',
    2 => 'Все',
];

$clubList = [
    10000001 => 'Склад',
    10000002 => 'Советская (1 этаж)',
    10000003 => 'Советская (11 этаж)',
    10000004 => 'Студенческая',
    10000005 => 'Версаль',
    10000006 => 'Кирова',
    10000007 => 'Плюс',
    10000108 => 'Шамшиных'
];

$form = new \yii\widgets\ActiveForm();
?>
<div class="filter-list">
 <?php $form::begin([
        'method' => 'get'
    ]);?>
    <!--
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-6">
                <?= $form
                    ->field($filter, "type")
                    ->DropDownList(
                        $orderTypeList,[
                        'prompt' => 'Заказы из',
                    ])->label('Заказы_из');?>
            </div>
        </div>-->
         <div class="row">
              <?= $form->field($filter, "staff",['options'=>['class'=>'form-group col-xs-12 col-md-3 staff']])->radioList($userType,[])->label('Покупатели');?>
              <?= $form->field($filter, "dateStatus",['options'=>['class'=>'form-group col-xs-12 col-md-2']])->DropDownList(['Оформления','Доставки'],[])->label('Дата');?>
              <div class="col-xs-12 col-md-3">
                 <label class="control-label">Период</label>
                 <?= DatePicker::widget([
                     'name' => 'OwnerOrderFilter[dateStart]',
                     'value' => $filter->dateStart,
                     'type' => DatePicker::TYPE_RANGE,
                     'name2' => 'OwnerOrderFilter[dateEnd]',
                     'value2' => $filter->dateEnd,
                     'language'=>Yii::$app->language,
                     'separator'=>'-',
                     'convertFormat'=>true,
                     'pluginOptions' => [
                         'autoclose'=>true,
                         'format' => 'yyyy-MM-dd',

                     ]
                 ])
                 ?>
             </div>
         </div>
        <div class="row">
            <?= $form->field($filter,'orderId',['options'=>['class'=>'form-group col-xs-12 col-md-3']])->textInput()->label('Номер заказа');?>
            <?= $form->field($filter, "status_id",['options'=>['class'=>'form-group col-xs-12 col-md-3']])->DropDownList($orderItemStatusList,['prompt' => 'выберите статус',])->label('Статус');?>
            <div class="col-xs-12 col-md-4 order-report-filter-shop-list">
                <div class="form-group field-shops-id">
                    <label class="control-label" for="shops-id">Поставщик</label>
                    <?= Select2::widget([
                        'model' =>$filter,
                        'attribute' => 'shops',
                        //'name'=>'OwnerOrderFilter[shops][id]',
                        'data' =>  \yii\helpers\ArrayHelper::map(\app\modules\managment\models\Shops::find()->all(), 'id', 'name'),
                        'options' => [
                            'placeholder' => 'Выбрать поставщика',
                            'multiple'=>true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])?>
                    <div class="help-block"></div>
                </div>
                <div class="order-report-filter-shop-children">
                    <?php /*
                    if(!empty($filter->shops)){
                        $i = 0;
                        foreach ($filter->shops as $shopId => $shopName) {?>
                        <div class="order-report-filter-shop-element" data-shop-id="<?= $shopId?>">
                            <input type="hidden" name="OwnerOrderFilter[shops][id][<?= $i?>]" value="<?= $shopId?>">
                            <?= $shopName?>
                            </div><?php
                            $i++;
                        }
                    }*/?>
                </div>
            </div>
        </div>
        <div class="form-group"><?= \yii\helpers\Html::submitButton('Найти', ['class' => 'btn btn-primary ']) ?></div>
        <div class="clear"></div>
    <?php
    $form::end();?>
</div>
