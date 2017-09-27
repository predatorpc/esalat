<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="tags-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['readonly' => true])->label('ID') ?>

    <?= $form->field($model, 'group_id')->DropDownList(ArrayHelper::map($groups,'id','name'))->label(Yii::t('admin', 'Группа свойств')) ?>
    <?= $form->field($model, 'value')->textInput(['maxlength' => true])->label(Yii::t('admin', 'Значение')) ?>

    <?= $form->field($model, 'status')->checkbox()->label(Yii::t('admin', 'Статус')) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
