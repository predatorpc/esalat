<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\modules\common\models\User;

$this->title = Yii::t('app', 'Регистрация');
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
            <?=$form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(),
                ['mask' => Yii::t('app','+79999999999')]
            )->label(Yii::t('app', 'Номер телефона'))->hint(Yii::t('app', 'Внимание! Номер телефона вводится без +7/8')) ?>
            <?php //= $form->field($model, 'phone')->textInput(['maxlength' => 10,'autofocus' => true,'class'=>'form-control placeholder phone','placeholder'=>"Номер телефона",'data-text'=>"Номер телефона"])->label('')->hint('Внимание! Номер телефона вводится без +7/8') ?>
            <?= (!empty(Yii::$app->session['signup-error']['err']) && Yii::$app->session['signup-error']['err'] == 'phone') ? '<div class="has-error"><p class="help-block help-block-error">'.Yii::t("app", "Такой телефон уже зарегистрирован в системе!").'</p></div> ':''?>
            <?=!empty($model->errors['phone']) && !empty($_POST['SignupForm']['phone']) ? '<a href="/site/restore-password">Забыли пароль?</a>' : ''?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => 255,'class'=>'form-control placeholder','placeholder'=>Yii::t('app', 'Фамилия Имя Отчество'),'data-text'=>Yii::t('app', 'Фамилия Имя Отчество')])->label('')->hint('') ?>
            <?= $form->field($model, 'email')->input('email',['maxlength' => 255,'class'=>'form-control placeholder','placeholder'=>"E-mail",'data-text'=>"E-mail"])->label('')->hint('') ?>
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => 32,'class'=>'form-control placeholder','placeholder'=>Yii::t('app', 'Пароль'),'data-text'=>Yii::t('app', 'Пароль')])->label('')->hint('') ?>
            <?= $form->field($model, 'agreement')->checkbox(['id' => 'agree'])->label(Yii::t('app', 'Я ознакомлен и согласен').' <a href=\'http://www.Esalad.ru/static/page/agreement\' target=\'_new\'>'.Yii::t('app', 'с пользовательским соглашением.').'</a>')->hint('') ?>


    <div style="display: none"><?php if(!empty(Yii::$app->session['signup-error'])) unset($_SESSION['signup-error'])?></div>

    <?php // = $form->field($model, 'reCaptcha', ['options' => ['id' => 'gre' ],'enableAjaxValidation' => false])->widget(\himiklab\yii2\recaptcha\ReCaptcha::className(['siteKey' => Yii::$app->reCaptcha->siteKey])) ?>
    <br>
    <!-- div><p class="help-block help-block-error" style="color: #a94442" id="errno"></p></div -->

    <div class="clear"></div>
    <div class="form-group"><button type="submit" id="agree_button" class="button_oran center" onclick="return modal_form_action('signup-form','submitsignup');"><?=\Yii::t('app', 'Зарегистрировать')?></button></div>
    <?php ActiveForm::end(); ?>
</div> <!--/Форма-->


