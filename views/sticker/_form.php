<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
/* @var $this yii\web\View */
/* @var $model app\modules\catalog\models\Sticker */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sticker-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <img src="/files/sticker/<?=$model->id?>.png" alt="..." class="img-rounded">
    </div>
    <div class="form-group">
        <?= $form->field($model, 'iconFiles')->fileInput()->label('Загрузки иконок')->hint(Yii::t('app','Вы можете загрузить в формате png (макс. 5 мб).'))?>
    </div>
    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
