<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var app\models\gbUser $model
 * @var yii\widgets\ActiveForm $form
 */
$this->title = 'Заказать звонок';
?>
<?php if(Yii::$app->session->hasFlash('success')):?>
    <div class="alert alert-success"> <?= Yii::$app->session->getFlash('success')?></div> <!-- For success message -->
<?php else: ?>

<!--Форма-->
<div class="form___gl gb-user-form">
    <?php $form = ActiveForm::begin(['options' => ['class' => 'call-form']]); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 124 ,'class'=>'form-control placeholder center', 'autofocus' => true ,'placeholder'=>"Имя",'data-text'=>"Имя"])->label('')?>
    <?= $form->field($model, 'phone',['template' => '<span class="phone">+7</span>{input}{error}{hint}'] )->textInput(['maxlength' => 10 ,'class'=>'form-control placeholder phone center', 'autofocus' => true ,'id' => 'phone','placeholder'=>"Телефон",'data-text'=>"Телефон"])->label('')?>
    <div class="form-group"><button type="submit" class="button_oran center" onclick="return modal_form_action('call-form','call');">Перезвонить</button></div>
    <?php ActiveForm::end(); ?>
</div> <!--/Форма-->

<?php endif; ?>