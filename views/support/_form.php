<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Messages */
/* @var $form yii\widgets\ActiveForm */


$arrStat = [
    [ 'id' => '0' , 'status' => Yii::t('admin', 'Не обработано'), ],
    [ 'id' => '1' , 'status' => Yii::t('admin', 'Обработано'), ],
];

?>

<div class="messages-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'order')->textInput(['maxlength' => true,'readonly' => true])->label(Yii::t('admin', 'Номер заказа:')); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'readonly' => true])->label(Yii::t('admin', 'Имя:')); ?>
    <?= $form->field($model, 'topic')->textInput(['maxlength' => true,'readonly' => true])->label(Yii::t('admin', 'Тема:')); ?>
    <?= $form->field($model, 'text')->textarea(['rows' => 6, 'readonly' => true])->label(Yii::t('admin', 'Вопрос:')); ?>
    <?= $form->field($model, 'answer')->textarea(['rows' => 6])->label(Yii::t('admin', 'Ответить')); ?>
    <?=$form->field($model, 'status')->DropDownList(ArrayHelper::map($arrStat,'id','status'))->label(Yii::t('admin', 'Статус')); ?>
    <?php //$form->field($model, 'status')->checkbox()->label('Статус')->hint('Сюда вводите статус'); ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
