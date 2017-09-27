<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\actions\models\ActionsParamsValue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="actions-params-value-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'action_id')->textInput() ?>

    <?= $form->field($model, 'param_id')->textInput() ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_user')->textInput() ?>

    <?= $form->field($model, 'updated_user')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
