<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lists */
/* @var $form yii\widgets\ActiveForm */

use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

?>

<div class="lists-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput(['readonly' => true, 'value' => Yii::$app->user->getId()]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php //= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput() ?>

    <?php //= $form->field($model, 'change')->textInput() ?>

    <?= $form->field($model, 'list_type')->textInput() ?>

    <?php //= $form->field($model, 'level')->textInput() ?>

    <?php //= $form->field($model, 'date_create')->textInput() ?>

    <?php //= $form->field($model, 'date_update')->textInput() ?>

    <?= $form->field($model, 'show_banners')->checkbox() ?>

    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin','Добавить') : Yii::t('admin','Редактировать'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin','Назад'), '/list', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

