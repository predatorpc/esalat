<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var app\models\gbUser $model
 * @var yii\widgets\ActiveForm $form
 */
$this->title = Yii::t('app', 'Хамелеон');
?>
<div id="success"> </div> <!-- For success message -->
<h1>Хамелеон</h1>
<!--Форма-->
<div class="form___gl gb-user-form">
    <?php $form = ActiveForm::begin(['options' => ['class' => 'login-form']]); ?>
    <?= $form->field($model, 'phone',['template' => '<span class="phone">'.Yii::t('app','+7').'</span>{input}{error}{hint}'] )->textInput(['maxlength' => 10 ,'type'=>'tel', 'class'=>'form-control placeholder phone', 'autofocus' => true ,'id' => 'phone','placeholder'=>Yii::t('app', 'Телефон'),'data-text'=>Yii::t('app', 'Телефон')])->label('')?>
    <div class="form-grou" style="text-align: center;">
        <button type="submit" class="button_oran"><?=\Yii::t('app', 'Вход')?></button>
    </div>
    <?php ActiveForm::end(); ?>
</div> <!--/Форма-->

