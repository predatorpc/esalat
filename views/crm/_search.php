<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ShopsCallbackSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shops-callback-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'shop_id') ?>

    <?= $form->field($model, 'action') ?>

    <?= $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'contact') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
