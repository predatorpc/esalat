<?php

use kartik\date\DatePicker;
use kartik\widgets\Select2;
use kartik\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use app\modules\shop\models\Orders;
use app\modules\catalog\models\Codes;
$userType = [
    0 => 'Клиенты',
    1 => 'Сотрудники',
    2 => 'Все',
];

$orderItemStatusList = \yii\helpers\ArrayHelper::map(\app\modules\shop\models\OrdersStatus::find()->where(['status' => 1])->all(),'id','name');
$orderItemStatusList[999] = Yii::t('admin', 'Не обработан');
$orderItemStatusList['-1'] = Yii::t('admin', 'Отменен');
$orderItemStatusList['NO'] = Yii::t('admin', 'Не выдан');

$stores = \app\modules\managment\models\ShopsStores::find()
    ->select('address_id, name, id')
    ->where('shop_id = 10000001')
    ->andWhere('status = 1')
    ->all();
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
// Модальное окно;
Modal::begin([
    'header' => '<h3>Есть претензия!</h3>',
    'id'=>'orders',
    'size' => Modal::SIZE_SMALL,
]);
if(isset($_POST['modal_order'])) {
    $order_id = $_POST['order_id'];
    $order = Orders::findOne(intval($order_id));
    Html::beginForm(['/reports/order-new'], 'post', ['class' => 'modal-form-orders']);
    echo '<div class="alert alert-danger hidden_r"></div>';
    echo '<input class="order-id" type="hidden" value="'.$order_id.'" name="order_id">';
    //comments_call_center
    echo '<div class="form-group">
               <b>Комментарий</b>
               <textarea class="form-control comments" name="comments" rows="3" style="width: 100%; margin:3px 0px 3px 0px">'.($order->comments_call_center ? $order->comments_call_center : '' ).'</textarea>
             </div>';
    echo '<div class="form-group"><input class="negative_status" '.($order->negative_review ? 'checked':'').' type="checkbox" name="negative_status"><b style="position: relative;top: -2px;left: 5px;">Вкл./выкл. претензия</b></div>';
    echo '<div class="form-group"><button data-loading-text="Загрузка..." onclick="addNegative();" type="button" class="btn btn-info" style="float: right;">Сохранить</button></div>';
    echo '<div class="clear"></div>';
    Html::endForm();
}
Modal::end();


$form = new \yii\widgets\ActiveForm();
?>
<h1><?= Html::encode($this->title) ?></h1>


<div class="filter-header">


    <div class="calendar-fast">
        <a class="dashed" href="<?=Url::to(['reports/order-new', 'OwnerOrderFilter[dateStart]' => Date("Y-m-d"), 'OwnerOrderFilter[dateEnd]' => Date("Y-m-d")]);?>">Сегодня</a>|
        <a class="dashed" href="<?=Url::to(['reports/order-new', 'OwnerOrderFilter[dateStart]' => Date('Y-m-d', strtotime('-1 day')), 'OwnerOrderFilter[dateEnd]' => Date('Y-m-d', strtotime('-1 day'))]);?>">Вчера</a>|
        <a class="dashed" href="<?=Url::to(['reports/order-new', 'OwnerOrderFilter[dateStart]' => Date('Y-m-d', strtotime('-2 day')), 'OwnerOrderFilter[dateEnd]' => Date('Y-m-d', strtotime('-2 day'))]);?>">Позавчера</a>|
        <a class="dashed" href="<?=Url::to(['reports/order-new', 'OwnerOrderFilter[dateStart]' => Date('Y-m-d', strtotime('-1 week')), 'OwnerOrderFilter[dateEnd]' => Date('Y-m-d')]);?>">Прош. неделя</a>|
        <a class="dashed" href="<?=Url::to(['reports/order-new', 'OwnerOrderFilter[dateStart]' => Date('Y-m-d', strtotime('-1 month')), 'OwnerOrderFilter[dateEnd]' => Date("Y-m-d")]);?>">Прош. месяц</a>|

        <a class="dashed" href="/reports/order-new"><b>Сбросить фильтр</b></a>
    </div>

    <div class="shops-search">
        <div class="content-f" >
    <?php $form = ActiveForm::begin(
        [
            'method' => 'get',
            'action' => '/reports/order-new',
            'fieldConfig' => [
                'labelOptions' => ['class' => 'label-form'],
                'template' => '{label}{hint}{input}',
            ],

    ]); ?>
         <div class="row">
            <?= $form->field($filter, "dateStatus",['options'=>['class'=>'form-group col-md-2 col-sm-6 col-xs-12']])->DropDownList(['Оформления','Доставки'],['class'=>'form-control input-sm'])->label('Дата');?>
            <div class="form-group col-md-3 col-sm-6 col-xs-12">
            <label class="control-label">Период</label>
            <?= DatePicker::widget([
                'name' => 'OwnerOrderFilter[dateStart]',
                'value' => date('d.m.Y',strtotime($filter->dateStart)),
                'type' => DatePicker::TYPE_RANGE,
                'name2' => 'OwnerOrderFilter[dateEnd]',
                'value2' => date('d.m.Y',strtotime($filter->dateEnd)),
                'language'=>'ru', //Yii::$app->language,
                'options'=>['class'=>'input-sm','placeholder' => 'с'],
                'options2'=>['class'=>'input-sm','placeholder' => 'по'],
                'separator'=>'-',
                'convertFormat'=>true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.MM.yyyy',

                ]
            ]);
            ?>
            </div>
            <?=$form->field($filter,'store_id',['options'=>['class'=>'form-group col-md-2 col-sm-3 col-xs-12 mobile-hidden']])->dropDownList(ArrayHelper::map($stores,'id','name'),['prompt' => 'выберите клуб','class'=>'form-control input-sm'])->label('Локация');?>
            <?=$form->field($filter,'orderId',['options'=>['class'=>'form-group col-md-2 col-sm-3 col-xs-12']])->textInput(['class'=>'form-control input-sm'])->label('Номер заказа');?>
            <?php
//                $arStatus = [''=> 'Все', 'NULL' => 'Не обработан','NO'=> 'Не выдан','-1' => 'Отменен'];
//                if(!empty($status)){
//                    $arStatus = ArrayHelper::merge($arStatus,$orderItemStatusList);
//                }
            ?>
            <?=$form->field($filter,'status_id',['options'=>['class'=>'form-group col-md-2 col-sm-3 col-xs-12']])->DropDownList($orderItemStatusList,['prompt' => 'выберите статус','class'=>'form-control input-sm'])->label('Статус заказа');?>
            <div class="content-form-checkbox form-group col-md-3 col-sm-3 col-xs-12">
                <?= $form->field($filter, 'not_our_shops',['options'=>['class'=>'form-group col-md-6 col-sm-6 col-xs-6']])->checkbox(['label'=>'Не наш товар']);?>
                <?= $form->field($filter, 'not_free_delivery',['options'=>['class'=>'form-group col-md-6 col-sm-6 col-xs-6']])->checkbox(['label'=>'Платная доставка']);?>
                <?= $form->field($filter, 'our_shops',['options'=>['class'=>'form-group col-md-6 col-sm-6 col-xs-6']])->checkbox(['label'=>'Наш товар']);?>
                <?php //= $form->field($filter, 'group',['options'=>['class'=>'form-group col-md-6 col-sm-6 col-xs-6']])->checkbox(['label'=>'Группировать']);?>
            </div>

             <div class="content-form-select form-group col-md-3 col-sm-4 col-xs-12">
                <label class="control-label" for="shops-id">Поставщик</label>
                <?= Select2::widget([
                    'model' =>$filter,
                    'attribute' => 'shops',
                    'language' => 'ru',
                    'size'=>Select2::SMALL,
                    'class'=>'form-control input-sm',
                    //'name'=>'OwnerOrderFilter[shops][id]',
                    'data' =>  \yii\helpers\ArrayHelper::map(\app\modules\managment\models\Shops::find()->all(), 'id', 'name'),
                    'options' => [
                        'placeholder' => 'Выбрать поставщика',
                        'multiple'=>true,
                    ],

                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])?>
             </div>
            <?= $form->field($filter, 'good_id',['options'=>['class'=>'form-group col-md-2 col-sm-3 col-xs-12']])->textInput(['class'=>'form-control input-sm'])->label('ID товара');?>

            <?=$form->field($filter,'user_id',['options'=>['class'=>'form-group col-md-2 col-sm-2 col-xs-12']])->textInput(['class'=>'form-control input-sm'])->label('ID пользователя');?>

             <?php /*<div class="content-form-select form-group col-md-2 col-sm-2 col-xs-12">
                 <label class="control-label" for="shops-id">Пользователь</label>
                 <?=$form->field($filter, 'user_id')->widget(Select2::classname(), [
                     'data' => ArrayHelper::map(\app\modules\common\models\User::find()->where(['status'=>1])->asArray()->All(),'id','name'),
                     'options' => ['placeholder' => 'Выберите...'],
                     'pluginOptions' => [
                         'allowClear' => true
                     ],
                 ])->label(false);
                 ?>
             </div> */?>

            <?=$form->field($filter,'staff',['options'=>['class'=>'form-group content-form-radio col-md-2 col-sm-3 col-xs-6']])->radioList($userType)->label(false);?>

            <div class="content-form-checkbox form-group col-md-2 col-sm-2 col-xs-6">
               <?= $form->field($filter, 'basket_sort')->checkbox(['label'=>'Сортировка как в корзине']);?>
               <?= $form->field($filter, 'no_promo')->checkbox(['label'=>'Без промо-кода']);?>
            </div>
             <?php
                $codes = Codes::find()->select('user_id, code')->where(['status'=>1])->All();
                $arCodes = [];
                foreach ($codes as $code){
                    if($code->user){
                        $arCodes[$code->code] = $code->code.' ('.$code->user->name.')';
                    }else{
                        $arCodes[$code->code] = $code->code;
                    }

                }
             ?>
             <div class="content-form-select form-group col-md-2 col-sm-2 col-xs-12">
                 <label class="control-label" for="shops-id">Промо код</label>
             <?=$form->field($filter, 'code')->widget(Select2::classname(), [
             'data' => $arCodes,
             'options' => ['placeholder' => 'Выберите...'],
             'pluginOptions' => [
             'allowClear' => true
             ],
             ])->label(false);?>
             </div>

            <?=$form->field($filter,'addressClub',['options'=>['class'=>'form-group content-form-radio col-md-2 col-sm-3 col-xs-12 mobile-hidden']])->dropDownList(ArrayHelper::map($stores,'address_id','name'),['prompt' => 'выберите клуб','class'=>'form-control input-sm'])->label('Доставка в клуб');?>

            <?=$form->field($filter,'productType',['options'=>['class'=>'form-group content-form-radio col-md-2 col-sm-3 col-xs-12']])->dropDownList(ArrayHelper::map(\app\modules\catalog\models\GoodsTypes::find()->where(['status'=>1])->All(),'id','name'),['prompt' => 'выберите тип','class'=>'form-control input-sm'])->label('Тип');?>

            <?=$form->field($filter,'category',['options'=>['class'=>'form-group content-form-radio col-md-2 col-sm-2 col-xs-12']])->dropDownList(ArrayHelper::merge([''=>'Все'],$arCategory),['class'=>'form-control input-sm'])->label('Категория');?>

            <div class="form-group col-md-2 col-sm-12 col-xs-12"> <button type="submit" class="btn btn-primary button-mod">Сформировать</button></div>
             <div class="clear"></div>
         </div>
    <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>