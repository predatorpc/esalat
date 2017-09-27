<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="useradmin-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php //= $form->field($model, 'id') ?>

    <?php //= $form->field($model, 'name') ?>

    <?php // = $form->field($model, 'fullname') ?>

    <?php // = $form->field($model, 'auth_key') ?>

    <?php


    if(empty(Yii::$app->request->get('start_date')) || empty(Yii::$app->request->get('end_date'))) {
        $dateValue = date("d.m.Y", strtotime("now"));
        $dateValue2 = date("d.m.Y", strtotime("now"));
    }
    else{
        $dateValue = date("d.m.Y", strtotime(Yii::$app->request->get('start_date')));
        $dateValue2 = date("d.m.Y", strtotime(Yii::$app->request->get('end_date')));
    }


    echo '<label>Выберите период</label>';
    echo \kartik\date\DatePicker::widget([
        'type' => \kartik\date\DatePicker::TYPE_RANGE,
        'name' => 'start_date',
        'name2' => 'end_date',
        'language' => 'ru',
        'value' => $dateValue,
        'value2' =>  $dateValue2,
        'options' => ['placeholder' => Yii::t('admin', 'Выберите период')],
        'pluginOptions' => [
            'format' => 'dd.mm.yyyy',
            'todayHighlight' => true
        ]
    ]);

    ?>


    <?php // echo $form->field($model, 'password_reset_token') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('admin', 'Поиск'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Сброс фильтра'), '/user/index', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
