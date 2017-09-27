<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\TimePicker;
use app\modules\managment\models\ShopStoresTimetable;


/* @var $this yii\web\View */
/* @var $model app\modules\managment\models\ShopStoresTimetable */
/* @var $form yii\widgets\ActiveForm */
$days = [1=>'Понедельник',
    2 => 'Вторник',
    3 => 'Среда',
    4 => 'Четверг',
    5 => 'Пятница',
    6 => 'Суббота',
    7 => 'Воскресение'];
$existDays = ArrayHelper::getColumn(ShopStoresTimetable::find()->where(['store_id'=>$model->store_id])->All(),'day');
foreach ($existDays as $day){
    if(isset($model->day )&& $day == $model->day){
        continue;
    }
    unset($days[$day]);
}
?>

<div class="shop-stores-timetable-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'store_id')->hiddenInput(); ?>

    <?= $form->field($model, 'day')->dropDownList($days); ?>

    <?= $form->field($model, 'time_begin')->widget(TimePicker::classname(), ['pluginOptions' => [
        'showSeconds' => false,
        'showMeridian' => false,
        'minuteStep' => 1,
        'secondStep' => 5,]]);?>
    <?= $form->field($model, 'time_end')->widget(TimePicker::classname(), ['pluginOptions' => [
        'showSeconds' => false,
        'showMeridian' => false,
        'minuteStep' => 10,
        'secondStep' => 5,]]);?>



    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
