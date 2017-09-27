<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\modules\common\models\User;

$this->title = 'Регистрация';
//$this->params['breadcrumbs'][] = $this->title;
?>



<div id="success"> </div> <!-- For success message -->
<!--Форма-->
<div class="form___gl gb-user-form">
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'signup-form'],
//        'enableAjaxValidation' => true,
//        'enableClientValidation' => false,
//        'validateOnSubmit' => false,
    ]); ?>
            <?= $form->field($model, 'phone',['template' => '<span class="phone">+7</span>{input}{error}{hint}'])->textInput(['maxlength' => 10,'autofocus' => true,'class'=>'form-control placeholder phone','placeholder'=>"Номер телефона",'data-text'=>"Номер телефона"])->label('')->hint('Внимание! Номер телефона вводится без +7/8') ?>
            <?= (!empty(Yii::$app->session['signup-error']['err']) && Yii::$app->session['signup-error']['err'] == 'phone') ? '<div class="has-error"><p class="help-block help-block-error">Такой телефон уже зарегистрирован в системе!</p></div>':''?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => 255,'class'=>'form-control placeholder','placeholder'=>"Ваше имя",'data-text'=>"Ваше имя"])->label('')->hint('') ?>
            <?= $form->field($model, 'email')->input('email',['maxlength' => 255,'class'=>'form-control placeholder','placeholder'=>"E-mail",'data-text'=>"E-mail"])->label('')->hint('') ?>
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => 32,'class'=>'form-control placeholder','placeholder'=>"Пароль",'data-text'=>"Пароль"])->label('')->hint('') ?>
    <div style="display: none"><?php if(!empty(Yii::$app->session['signup-error'])) unset($_SESSION['signup-error'])?></div>

<!--    --><?//= $form->field($model, 'reCaptcha', ['options' => ['id' => 'gre' ],'enableAjaxValidation' => false])->widget(\himiklab\yii2\recaptcha\ReCaptcha::className()) ?>
    <div><p class="help-block help-block-error" style="color: #a94442" id="errno"></p></div>

    <div class="clear"></div>
    <div class="form-group"><button type="submit" class="button_oran center" onclick="return modal_form_action('signup-form','submitsignup');">Зарегистрировать</button></div>
    <?php ActiveForm::end(); ?>
</div> <!--/Форма-->


