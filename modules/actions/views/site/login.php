<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var app\models\gbUser $model
 * @var yii\widgets\ActiveForm $form
 */
$this->title = 'Вход';
?>
<div id="success"> </div> <!-- For success message -->
<!--Форма-->

<div class="form___gl gb-user-form">
    <?php $form = ActiveForm::begin(['options' => ['class' => 'login-form']]); ?>
        <?= $form->field($model, 'phone',['template' => '<span class="phone">+7</span>{input}{error}{hint}'] )->textInput(['maxlength' => 10 ,'class'=>'form-control placeholder phone', 'autofocus' => true ,'id' => 'phone','placeholder'=>"Телефон",'data-text'=>"Телефон"])->label('')?>
        <?= $form->field($model, 'password')->passwordInput(['maxlength' => 32,'class'=>'form-control placeholder','placeholder'=>"Пароль",'data-text'=>"Пароль"])->label('') ?>
        <?php /*$form->field($model, 'rememberMe',['template' => '{input}  {label}<a href="#" class="link hidden">Я забыл пароль</a>'] )->checkbox(['label' => ''])->label('Запомнить меня') */?>
        <div class="form-grou" style="text-align: center;">
            Введите данные для входа или зарегистрируйтесь<br><br>
            <button type="submit" class="button_oran" onclick="return modal_form_action('login-form','submitlogin');">Вход</button>
            <button type="button" class="button_oran" onclick="return window_show('signup','Регистрация');">Регистрация</button>
        </div>
    <?php ActiveForm::end(); ?>
</div> <!--/Форма-->

