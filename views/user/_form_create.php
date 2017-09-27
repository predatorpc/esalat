<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\common\models\UserAdmin;

if(Yii::$app->user->can('GodMode')){
    $flash = Yii::$app->session->getAllFlashes();
    foreach ($flash as $item) {
        \app\modules\common\models\Zloradnij::print_arr($item);
    }
}
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Useradmin */
/* @var $form yii\widgets\ActiveForm */
?>



<div class="useradmin-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php  $params = ['prompt' => Yii::t('admin', 'Выбрать категорию')]; ?>

    <?php //$params1 = [ '0' => 'Не активен', '1' => 'Активен'] ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => 10])->hint(Yii::t('admin', 'Номер телефона вводится без 8 и без +7')) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput()->label(Yii::t('admin', 'Новый пароль'))->hint(Yii::t('admin', 'Оставьте без изменений, если не нужно менять')) ?>
    <?= $form->field($model, 'email')->textInput()->hint(Yii::t('admin', 'Электронная почта')) ?>
    <?= $form->field($model, 'role')->DropDownList(ArrayHelper::map(Yii::$app->authManager->getRoles(),'name','description'), $params)  ?>
    <?= $form->field($model, 'status')->CheckBox() ?>
    <?= $form->field($model, 'sms')->CheckBox() ?>
    <?php //= $form->field($model, 'email')->CheckBox() ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Добавить') : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
