<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var app\models\gbUser $model
 * @var yii\widgets\ActiveForm $form
 */
$this->title = 'Промо код';
?>
<div class="content">
    <h1 class="title"><?=$this->title?></h1>
    <div id="promo">
            <div class="text2">
                <!-- h3 align="center">Экономь и зарабатывай с промокодом Extremeshop!</h3>
                <p> Личный промокод дает скидку 5% на покупки<span class="error">*</span>  в интернет-магазине ExtremeShop, а также позволяет зарабатывать с покупок друзей. </p>
                <p><b>Как это работает? </b><br>Поделитесь своим промокодом с близкими, и получайте кэшбэк с их покупок<span class="error">*</span>  на свой баланс в интернет-магазине ExtremeShop.</p>
                <p class="font_size"><span class="error">*</span>Скидка действует на товары, участвующие в акции.</p -->
            </div>
          <?php if(!Yii::$app->user->isGuest):?>




              <?php if(!empty($promo->code)): ?>
                  <div class="show_promocode">
                    <div class="text2 success"> Поздравляем! Ваш промокод успешно создан :  <strong><?=$promo->code?></strong> </div>
                  </div>
              <?php else: ?>
                  <!--Форма-->
                  <div class="form___gl hidden">
                      <?php $form = ActiveForm::begin(['options' => ['class' => 'promo-form']]); ?>
                      <?php //$form->field($model, 'email')->textInput(['maxlength' => 124 ,'class'=>'form-control placeholder center', 'autofocus' => true ,'placeholder'=>"Введите Ваш e-mail",'data-text'=>"Введите Ваш e-mail"])->label(false); ?>
                      <div class="form-group"><button type="submit" class="button_oran center" onclick="return modal_form_action('promo-form','site/promo');">Получить</button></div>
                      <?php ActiveForm::end(); ?>
                  </div> <!--/Форма-->
              <?php endif; ?>

         <?php else: ?>
              <br>
              <br>
              <div style="font-size: 12px;">Для получения промокода необходимо <a href="/" onclick="return window_show('login','Вход');">войти</a> или <a href="/" onclick="return window_show('signup','Регистрация');">зарегистрироваться</a></div>
         <?php endif; ?>

    </div>
    <div class="clear"></div>
</div>
