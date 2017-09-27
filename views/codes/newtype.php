<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model app\modules\catalog\models\codes */

$this->title = Yii::t('admin', 'Создать тип промо-кода');
$this->params['breadcrumbs'][] = $this->title;
$shops = \app\modules\managment\models\Shops::find()->where(['status'=>1])->All();
?>

<div class="codes">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model,'name')->label('Название типа');?>

    <?=$form->field($model,'shop_id')->label('Магазин')->dropDownList(ArrayHelper::map($shops,'id','name'));?>

    <?=$form->field($model,'money_discount')->label('Сумма скидки, руб.');?>

    <div class="form-group">
            <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary']) ?> <?= Html::a(Yii::t('admin', 'Назад'), ['index'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>