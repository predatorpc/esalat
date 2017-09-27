<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\ShopsSearch */
/* @var $form yii\widgets\ActiveForm */

$usetype = [
    ['id' => '-1',
        'name' => Yii::t('admin', 'Все'),
    ],
    ['id' => 0,
        'name' => Yii::t('admin', 'Покупки'),
    ],
    ['id' => 1,
        'name' => Yii::t('admin', 'Продажи'),
    ],
];


$typeof = [
//    [ 'id' => 0,
//      'name' => '--',
//    ],
    ['id' => 1,
        'name' => Yii::t('admin', 'Администратор клуба'),
    ],
    ['id' => 2,
        'name' => Yii::t('admin', 'Персональный тренер'),
    ],
    ['id' => 3,
        'name' => Yii::t('admin', 'Тренер групповых тренировок'),
    ],
    ['id' => 4,
        'name' => Yii::t('admin', 'Управление'),
    ],
    ['id' => 5,
        'name' => Yii::t('admin', 'Фитнес консультант'),
    ],

];


?>
<div id="promo-calendar">
    <div class="filter-date">
        <div class="calendar-fast">
            <a class="dashed" href="<?=Url::to(['shop-management/promo-code-statistic', 'CodesSearch[dateStart]' => Date("d.m.Y"), 'CodesSearch[dateStop]' => Date("d.m.Y"), 'CodesSearch[club]' => 0, 'CodesSearch[usetype]' => 0]);?>">Сегодня</a>
            <a class="dashed" href="<?=Url::to(['shop-management/promo-code-statistic', 'CodesSearch[dateStart]' => Date('d.m.Y', strtotime('-1 day')), 'CodesSearch[dateStop]' => Date('d.m.Y', strtotime('-1 day')), 'CodesSearch[club]' => 0, 'CodesSearch[usetype]' => 0]);?>">Вчера</a>
            <a class="dashed" href="<?=Url::to(['shop-management/promo-code-statistic', 'CodesSearch[dateStart]' => Date('d.m.Y', strtotime('-2 day')), 'CodesSearch[dateStop]' => Date('d.m.Y', strtotime('-2 day')), 'CodesSearch[club]' => 0, 'CodesSearch[usetype]' => 0]);?>">Позавчера</a>
            <a class="dashed" href="<?=Url::to(['shop-management/promo-code-statistic', 'CodesSearch[dateStart]' => Date('d.m.Y', strtotime('-1 week')), 'CodesSearch[dateStop]' => Date('d.m.Y'), 'CodesSearch[club]' => 0, 'CodesSearch[usetype]' => 0]);?>">Прош. неделя</a>
            <a class="dashed" href="<?=Url::to(['shop-management/promo-code-statistic', 'CodesSearch[dateStart]' => Date('d.m.Y', strtotime('-1 month')), 'CodesSearch[dateStop]' => Date("d.m.Y"), 'CodesSearch[club]' => 0, 'CodesSearch[usetype]' => 0]);?>">Прош. месяц</a>
            <a class="dashed show-filter" >Фильтр</a>
        </div>
    </div>
    <div class="shops-search">
        <div class="content-f" style="display: none;">
        <?php $form = ActiveForm::begin([
            'action' => ['promo-code-statistic'],
            'method' => 'get',
        ]); ?>

        <label style="vertical-align: top;"><?= Yii::t('admin', 'От') ?>
            <?= DatePicker::widget([
            'name' => 'CodesSearch[dateStart]',
            'type' => DatePicker::TYPE_INPUT,
            'value' => date('Y-m-d', strtotime('-1 month')),
            'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd'
            ]
            ]);?>
        </label>

        <label style="vertical-align: top;"><?= Yii::t('admin', 'До') ?>
            <?= DatePicker::widget([
                'name' => 'CodesSearch[dateStop]',
                'type' => DatePicker::TYPE_INPUT,
                'value' => date('Y-m-d', strtotime('now')),
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]);?>

        </label>

        <br>

        <?php


        $stores = \app\modules\managment\models\ShopsStores::find()
            ->select('id, name')
            ->where('shop_id = 10000001')
            ->andWhere('status = 1')
            ->all();


        ?>

        <label style="vertical-align: top;"><?= Yii::t('admin', 'Клуб') ?>
            <?php

            $default = Yii::t('admin', 'Все');
            echo Html::dropDownList('CodesSearch[club]', !empty($_GET['CodesSearch']['club']) ? $_GET['CodesSearch']['club'] : $default, \yii\helpers\ArrayHelper::map(array_merge([['id' => '0', 'name' => Yii::t('admin', Yii::t('admin', 'Все'))]], $stores), 'id', 'name'), ['class' => 'form-control']) ?>
        </label>

        <label style="vertical-align: top;"><?= Yii::t('admin', 'Тип пользователя') ?>
            <?php
            $default_type = 0;
            echo Html::dropDownList('CodesSearch[typeof]', isset($_GET['CodesSearch']['typeof']) ? $_GET['CodesSearch']['typeof'] : $default_type, \yii\helpers\ArrayHelper::map(array_merge([['id' => '0', 'name' => '--']], $typeof), 'id', 'name'), ['class' => 'form-control']) ?>
        </label>


        <?php /*<label style="vertical-align: top;"><?= Yii::t('admin', 'Тип использования') ?>
            <?php
            $default_type = -1;
            echo Html::dropDownList('CodesSearch[usetype]', isset($_GET['CodesSearch']['usetype']) ? $_GET['CodesSearch']['usetype'] : $default_type, \yii\helpers\ArrayHelper::map($usetype, 'id', 'name'), ['class' => 'form-control']) ?>
        </label> */?>

        <label style="vertical-align: top; "><?= Yii::t('admin', 'Номер промокода') ?>
            <?php
            $default_code = 0;
            echo Html::textInput('CodesSearch[code]', isset($_GET['CodesSearch']['code']) ? $_GET['CodesSearch']['code'] : $default_code, ['class' => 'form-control']) ?>
        </label>

        <label style="vertical-align: top; "><?= Yii::t('admin', 'Учитывать покупки без промокода') ?>
            <?php
            $wocode = false;
            echo Html::checkbox('CodesSearch[wocode]', isset($_GET['CodesSearch']['wocode']) ? $_GET['CodesSearch']['wocode'] : $wocode, ['class' => 'form-control']) ?>
        </label>


        <?php // echo $form->field($model, 'description') ?>

        <?php // echo $form->field($model, 'phone') ?>

        <?php // echo $form->field($model, 'min_order') ?>

        <?php // echo $form->field($model, 'delivery_delay') ?>

        <?php // echo $form->field($model, 'delay') ?>

        <?php // echo $form->field($model, 'comission_id') ?>

        <?php // echo $form->field($model, 'comission_value') ?>

        <?php // echo $form->field($model, 'count') ?>

        <?php // echo $form->field($model, 'show') ?>

        <?php // echo $form->field($model, 'notice') ?>

        <?php // echo $form->field($model, 'registration') ?>

        <?php // echo $form->field($model, 'status') ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('admin', 'Поиск'), ['class' => 'btn btn-primary']) ?>
            <?php //= Html::a(Yii::t('admin', 'Откл. страницы'), Url::current() . '&p=0', ['class' => 'btn btn-primary']) ?>
            <?php //= Html::a(Yii::t('admin', 'Вкл. страницы'), '/shop-management/promo-code-statistic', ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('admin', 'Сбросить фильтр'), '/shop-management/promo-code-statistic?CodesSearch[usetype]=0&CodesSearch[club]=0', ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>