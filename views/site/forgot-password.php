<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var app\models\gbUser $model
 * @var yii\widgets\ActiveForm $form
 */
$this->title = Yii::t('app', 'Восстановление пароля');
?>
<?php \yii\widgets\Pjax::begin()?>
<div class="lists-index">

    <h4><?= Html::encode($this->title) ?></h4>

    <div id="success"> </div> <!-- For success message -->
    <!--Форма-->

    <div class="form___gl gb-user-form">
        <?php $form = ActiveForm::begin(['options' => ['class' => 'forgot-password-form']]); ?>
        <?= $form->field($model, 'phone',['template' => '<span class="phone" style="">+7</span>{input}{error}{hint}'] )->textInput(['maxlength' => 10 ,'class'=>'form-control placeholder phone', 'autofocus' => true ,'id' => 'phone','placeholder'=>Yii::t('app', 'Телефон'),'data-text'=>Yii::t('app', 'Телефон') ])->label('')?>
        <div class="form-grou" style="text-align: center;">
            <?=Yii::t('app', 'Введите свой телефон для получения кода восстановления пароля')?><br>
            <?=Yii::t('app', 'Внимание! Код восстановления действителен в течение семи минут.') ?><br><br>
            <button type="submit" class="button_oran"><?=Yii::t('app', 'Получить код') ?></button>
        </div>
        <?php ActiveForm::end(); ?>
    </div> <!--/Форма-->

</div>

<?php \yii\widgets\Pjax::end()?>