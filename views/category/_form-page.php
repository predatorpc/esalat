<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;
/* @var $this yii\web\View */
/* @var $model app\modules\pages\models\Pages */
/* @var $form yii\widgets\ActiveForm */
?>
<p><?= Html::a('Home', ['index-page'], ['class' => 'btn btn-primary']) ?></p>
<div class="pages-form">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation'   => false,
        'enableClientValidation' => false,
        'validateOnBlur'         => false,
        'validateOnType'         => false,
        'validateOnChange'       => false,
        'validateOnSubmit'       => true,
    ]); ?>
    <?= $form->field($model, 'page_id')->textInput() ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'text')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'editorOptions' => [
            'preset' => 'standard', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
            'inline' => false, //по умолчанию false
        ],

    ]) ?>
    <?= $form->field($model, 'seo_title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'seo_description')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'seo_keywords')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'status')->checkbox(['disabled' => false,]); ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : Yii::t('admin','Сохранить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>