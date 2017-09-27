<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ActionsPresentSave */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="actions-present-save-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php // = $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'basket_id')->textInput(['readonly' => true]) ?>

    <?php // = $form->field($model, 'present')->textInput() ?>

    <?php // = $form->field($model, 'card_number')->textInput(['maxlength' => true]) ?>

    <?php // = $form->field($model, 'create_date')->textInput() ?>

    <?php // = $form->field($model, 'update_date')->textInput() ?>

    <?php // = $form->field($model, 'bought_date')->textInput() ?>

    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
