<?php

use app\assets\AppAsset;
use app\modules\common\models\ModFunctions;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\pages\models\SignupForm;
use app\modules\common\models\User;

      Modal::begin([
                    'header' => '<h3>Ввод кодового слова</h3>',
                    'id' => 'license',
                    'class' => 'modal show',
                ]);


                echo "<p>Пожалуйста, введите кодовое слово и запомните его.<br><br> Кодовое слово используется для смены данных, телефона, а так же восстановления пароля.<br><br>
<span color='red'>Внимание!</span> Кодовое слово замене и восстановлению не подлежит.</p>";

                echo Html::beginForm(['/site/save-secret-word'], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
                echo Html::input('text', 'secretWord', '', ['class' => 'form-control', 'minlength' => 6, 'maxlength' => 255]);
                echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'hash-button']);
                echo Html::endForm();

                Modal::end();



?>

<form>
    <fieldset style="display: block; margin: 20px; padding: 40px; border: 2px groove;">

    <h1>Акция «Абонемент за покупки»</h1>

    <h3>Купите в нашем магазине товары на 5.500 рублей и получите в подарок абонемент в Extreme Fitness на один месяц!</h3>

<ul>
    <b>Условия проведения акции:</b><br>
    <li>Срок действия акции — с 8 по 14 августа 2016 года включительно.</li>
    <li>Для участия в акции необходимо совершить покупку в интернет-гипермаркете Esalad общим чеком от 5.500 рублей (включая доставку) и ввести промокод ФИТНЕС.</li>
    <li>В акции принимают участие товары, отмеченные знаком бонус.</li>
</ul>
        <br>
   <ul><b> Условия получения подарочного абонемента:</b><br>
    <li>Если Вы действующий клиент Extreme Fitness, то в период с 15 по 19 августа Ваш абонемент будет продлён на следующий месяц.</li>
    <li>Если Вы действующий клиент Extreme Fitness с оплаченным абонементом на год, то в период с 15 по 19 августа Ваш абонемент будет продлен на 13-й месяц.</li>
    <li>Если Вы не являетесь клиентом Extreme Fitness, в период с 15 по 19 августа с Вами свяжется наш специалист и расскажет, как получить подарочный абонемент Gold на один месяц.</li>
    <li>За две (и более) покупки, каждая на сумму 5.500 рублей, Вы можете получить два (и более) абонемента Gold (для новых клиентов) или продлить два (и более) действующих абонемента .</li>
    </ul>

    </p>
    </fieldset>
    <br>

    <p style="text-align: center; margin-bottom: 20px;">
        <input type="checkbox" value="0" id="agree"/>
        Я ознакомлен с условиями Акции&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <?php if(Yii::$app->user->isGuest){?>
            <button disabled class="button_oran" id="agree_button" onclick="return window_show('login','Вход');">Согласен</button>
        <?php } else { ?>
            <input type="button" disabled class="button_oran" id="agree_button" onclick="setTimeout(function(){window.location.href = '/site/agree';}, 500);" value="Согласен"/>
        <?php } ?>
    </p>

</form>
