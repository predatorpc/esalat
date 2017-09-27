<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var app\models\gbUser $model
 * @var yii\widgets\ActiveForm $form
 */
$this->title = Yii::t('app','Восстановление пароля');
?>

<div class="lists-index">

    <h4><?= Html::encode($this->title) ?></h4>

    <div id="success"><?= !empty($response) && $response == 'OK' ? Yii::t('app', 'Пароль успешно изменён, войдите в свой аккаунт через форму входа.') : ''?></div> <!-- For success message -->
    <!--Форма-->
    <?php
    if(empty($response) || $response == 'empty'){
        $model = new \app\modules\pages\models\RestorePasswordForm();
        ?>

        <div class="form___gl gb-user-form">
            <?php $form = ActiveForm::begin(['options' => ['class' => 'forgot-password-form']]); ?>
            <?= $form->field($model, 'code')->label(Yii::t('app','Код восстановления'))?>
            <?= $form->field($model, 'password')->label(Yii::t('app','Новый пароль'))?>
            <div class="form-grou" style="text-align: center;">
                <?=Yii::t('app',' Введите код, отправленный Вам в SMS и новый пароль'); ?><br><br>
                <button type="submit" class="button_oran"><?=Yii::t('app','Изменить пароль'); ?></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div> <!--/Форма-->

        <?php
//
//        $findUser = \app\modules\common\models\User::find()->where(['password_reset_token' => md5('57a9af4e1a40e')])->andWhere(['>=','updated_at',(time() - 60*7)])->one();
//        \app\modules\common\models\Zloradnij::print_arr($findUser);
//        $findUser = \app\modules\common\models\User::find()->where(['password_reset_token' => md5('57a9af4e1a40e')])->one();
//        \app\modules\common\models\Zloradnij::print_arr($findUser->updated_at - (time() + 60*7));
//        \app\modules\common\models\Zloradnij::print_arr($findUser);
    }
    ?>
</div>