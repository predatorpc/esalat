<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var app\models\gbUser $model
 * @var yii\widgets\ActiveForm $form
 */
$this->title = Yii::t('app', 'Вход');

?>
<div id="success"> </div> <!-- For success message -->
<!--Форма-->
<div class="form___gl gb-user-form">
    <?php $form = ActiveForm::begin(['options' => ['class' => 'login-form']]); ?>
        <?= $form->field($model, 'phone',['template' => '<span class="phone">'.Yii::t('app','+7').'</span>{input}{error}{hint}'] )->textInput(['maxlength' => 10 ,'type'=>'tel', 'class'=>'form-control placeholder phone','id' => 'phone','placeholder'=>Yii::t('app', 'Телефон'),'data-text'=>Yii::t('app', 'Телефон')])->label('')?>
        <?= $form->field($model, 'password')->passwordInput(['maxlength' => 32,'class'=>'form-control placeholder','placeholder'=>Yii::t('app', 'Пароль'),'data-text'=>Yii::t('app', 'Пароль')])->label('') ?>
        <?php /*$form->field($model, 'rememberMe',['template' => '{input}  {label}<a href="#" class="link hidden">Я забыл пароль</a>'] )->checkbox(['label' => ''])->label('Запомнить меня') */?>
        <div class="form-grou" style="text-align: center;"><?=\Yii::t('app', 'Введите данные для входа или зарегистрируйтесь')?> <br><br>
            <button type="submit" class="button_oran" onclick="return modal_form_action('login-form','submitlogin');"><?=\Yii::t('app', 'Вход')?></button>
            <button type="button" class="button_oran" onclick="return window_show('signup','<?=\Yii::t('app', 'Регистрация')?>');"><?=\Yii::t('app', 'Регистрация')?></button>
        </div>
    <?php ActiveForm::end(); ?>
    <div class="form-group" style="text-align: center;margin-top:20px;">
        <a class="btn-danger btn-sm" href="/forgot-password" style="text-decoration: none;color:#FFF;"><?=\Yii::t('app', 'Забыли пароль')?></a>
    </div>
</div> <!--/Форма-->

