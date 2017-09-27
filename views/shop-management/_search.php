<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ShopsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shops-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'type_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'name_full') ?>

    <?= $form->field($model, 'contract') ?>

    <?php // echo $form->field($model, 'tax_number') ?>

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
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
