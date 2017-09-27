<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
    <h2>Добавление нового пользователя</h2>
<?php $form = ActiveForm::begin(['enableAjaxValidation' => false]); ?>
    <span>Номер телефона</span>
<?php $model->phone = str_replace('+7','',$model->phone);?>
<?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
    'mask' => '999-999-99-99', ])->label(false)->hint(Yii::t('admin', 'Номер телефона в формате без +7/8, Пример: XXX-XXX-XX-XX')) ?>

<?= $form->field($model, 'name')->textInput(['placeholder' => 'Фамилия Имя Отчество'])->label(false) ?>

<?= $form->field($model, 'email')->textInput(['placeholder' => 'E-mail'])->label(false) ?>

<?= $form->field($model, 'password_hash')->passwordInput(['placeholder' => 'Пароль'])->label(false) ?>

<?= $form->field($model, 'invite_promo')->textInput(['placeholder' => 'Промокод'])->label(false) ?>

<?= $form->field($model, 'agree')->checkbox() ?>

<?= $form->field($model, 'autoreg')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Зарегистрировать', ['class' => 'btn btn-primary']) ?>
    </div>
<?php
    echo '<span style="color:green";>'.$success.'</span>';
?>

<?php ActiveForm::end(); ?>