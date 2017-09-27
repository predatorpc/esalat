<?php


$orderItemStatusList = \yii\helpers\ArrayHelper::map(\app\modules\shop\models\OrdersStatus::find()->where(['status' => 1])->all(),'id','name');
$orderItemStatusList['NULL'] = Yii::t('admin', 'Не обработан');
$orderItemStatusList[0] = Yii::t('admin', 'Отменен');
$orderItemStatusList['NO'] = Yii::t('admin', 'Не выдан');

$orderTypeList = [
    1 => Yii::t('admin', 'Сайт'),
    2 => Yii::t('admin', 'Терминалы'),
];

$userType = [
    0 => Yii::t('admin', 'Клиенты'),
    1 => Yii::t('admin', 'Сотрудники'),
    2 => Yii::t('admin', 'Все'),
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

$dateStart = $filter->checkParam('Orders','date') && !empty($filter->getFilterParams()['Orders']['date'][0]) ? $filter->getFilterParams()['Orders']['date'][0] : date('Y-m-d H:i');
$dateEnd   = $filter->checkParam('Orders','date') && !empty($filter->getFilterParams()['Orders']['date'][1]) ? $filter->getFilterParams()['Orders']['date'][1] : date('Y-m-d H:i');


//\app\modules\common\models\Zloradnij::print_arr($params);

$form = new \yii\widgets\ActiveForm();
$form::begin([

]);?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-6">
            <?= $form
                ->field($filter->checkParam('Orders','type') ? $modelOrderType : new \app\modules\shop\models\Orders(), "type")
                ->DropDownList(
                    $orderTypeList,[
                    'prompt' => Yii::t('admin', 'Заказы из'),
                ])->label(Yii::t('admin', 'Заказы_из'));?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?= $form
                ->field($filter, "staff")->radioList(
                    $userType,[

                ])->label(Yii::t('admin', 'Покупатели'));?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <?= $form
                ->field($filter, "dateStatus")
                ->DropDownList(
                    [Yii::t('admin', 'Оформления'), Yii::t('admin', 'Доставки')],[
                ])->label(Yii::t('admin', 'Дата'));?>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <?= $form
                ->field($modelOrderDateStart,'dateStart')
                ->widget(\yii\jui\DatePicker::className(),[
                    'clientOptions' => [
                        'class' => 'www',
                    ],
                    'language' => 'ru',
                    'dateFormat' => 'yyyy-MM-dd',
                ])
                ->label('От')?>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <div class="form-group field-codes-code">
                <label class="control-label" for="codes-code">До</label>
                <?= \yii\jui\DatePicker::widget([
                    'model' => $modelOrderDateEnd,
                    'attribute' => 'dateEnd',
                    'language' => 'ru',
                    'dateFormat' => 'yyyy-MM-dd',
                    'class' => 'form-control',
                ]);?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4"><?= $form->field($filter->checkParam('Orders','id') ? $modelOrderId : new \app\modules\shop\models\Orders(),'id')->textInput()->label(Yii::t('admin', 'Номер_заказа'));?></div>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
            <?= $form
                ->field($filter->checkParam('OrdersItems','status_id') ? $modelOrderItem : new \app\modules\shop\models\OrdersItems(), "status_id")
                ->DropDownList(
                    $orderItemStatusList,[
                    'prompt' => Yii::t('admin', 'выберите статус'),
                ])->label(Yii::t('admin', 'Статус'));?>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4"><?= $form->field($filter->checkParam('Codes','code') ? $modelPromoCode : new \app\modules\catalog\models\Codes(),'code')->textInput()->label('Промо-код');?></div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 order-report-filter-client-list">
            <div class="form-group field-user-name">
                <label class="control-label" for="client-id"><?= Yii::t('admin', 'Покупатель') ?></label>
                <input id="client-id" class="form-control" type="text" name="">
                <div class="help-block"></div>
            </div>
            <div class="order-report-filter-client-children"><?php
                if(!empty($filter->clients)){

                    $i = 0;
                    foreach ($filter->clients as $clientId => $clientName) {?>
                    <div class="order-report-filter-client-element" data-client-id="<?= $clientId?>">
                        <input type="hidden" name="Clients[id][<?= $i?>]" value="<?= $clientId?>">
                        <?= $clientName?>
                        </div><?php
                        $i++;
                    }
                }?>
            </div>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 order-report-filter-shop-list">
            <div class="form-group field-shops-id">
                <label class="control-label" for="shops-id"><?= Yii::t('admin', 'Поставщик') ?></label>
                <input id="shops-id" class="form-control" type="text" name="">
                <div class="help-block"></div>
            </div>
            <div class="order-report-filter-shop-children"><?php
                if(!empty($filter->shops)){

                    $i = 0;
                    foreach ($filter->shops as $shopId => $shopName) {?>
                    <div class="order-report-filter-shop-element" data-shop-id="<?= $shopId?>">
                        <input type="hidden" name="Shops[id][<?= $i?>]" value="<?= $shopId?>">
                        <?= $shopName?>
                        </div><?php
                        $i++;
                    }
                }?>
            </div>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
            <?= $form
                ->field($modelUserForStore->store_id ? $modelUserForStore: new \app\modules\common\models\User(), "store_id")
                ->DropDownList(
                    $clubList,[
                    'prompt' => 'Все',
                ])->label(Yii::t('admin', 'Клуб'));?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4"><?= $form->field($filter->checkParam('Orders','user_id') ? $modelOrderUser : new \app\modules\shop\models\Orders(),'user_id')->textInput()->label('User_ID');?></div>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
            <?= $form->field($filter,'productType')->checkboxList(\yii\helpers\ArrayHelper::map($filter->productTypeList,'id','name'))->label(Yii::t('admin', 'Тип товара'))?>
            <input class="form-control" type="text" name="" disabled>
        </div>
    </div>


    <div class="form-group">
        <?= \yii\helpers\Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
    </div>

<?php
$form::end();
