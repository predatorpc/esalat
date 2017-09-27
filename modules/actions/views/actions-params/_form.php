<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\actions\models\ActionsParams */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="actions-params-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList($model->getTypeAction()) ?>

    <?= $form->field($model, 'area')->dropDownList($model->getObjectList()) ?>

    <?= $form->field($model, 'currency')->dropDownList($model->getTypeList()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('actions', 'Create') : Yii::t('actions', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
