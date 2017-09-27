<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\widgets\MaskedInput;
use app\modules\common\models\User;
use app\modules\common\models\UserRoles;

//print_r($model);die();
$discont=[
    5=>'5%',
    10=>'10%',
    15=>'15%',
];
?>
<div class="actions-params-value-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'param_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'condition_value')->dropDownList($category)->label('Выбор категории') ?>

    <?= $form->field($model, 'discont_value')->dropDownList($discont)->label('Выбор скидки') ?>

    <?= $form->field($action, 'count_for_user')->textInput(['maxlength' => true])->label('Количество'); ?>

    <?= $form->field($model, 'basket_price')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'created_at')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'updated_at')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'created_user')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'updated_user')->hiddenInput()->label(false) ?>


<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Добавить') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

</div>

