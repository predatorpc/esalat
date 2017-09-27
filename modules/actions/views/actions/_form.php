<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\actions\models\ActionsParams;
use kartik\date\DatePicker;

//похабщина
if(!$model->isNewRecord){
    if(is_int($model->date_start)){
        $model->date_start = Date('d.m.Y', $model->date_start);
    }
    if(is_int($model->date_end)){
        $model->date_end = Date('d.m.Y', $model->date_end);
    }
}

//print_r($model);die();
/* @var $this yii\web\View */
/* @var $model app\modules\actions\models\Actions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="actions-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype'=>'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'priority')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type_promo_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'block')->checkbox()->label(Yii::t('actions', 'Stop Next Actions')) ?>

    <?= $form->field($model, 'accumulation')->checkbox()->label('') ?>

    <?= $form->field($model, 'accum_value')->textInput() ?>

    <?= $form->field($model, 'count_purchase')->checkbox()->label('') ?>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <!--
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <?= $form->field($model, 'periodic')->checkbox()->label(Yii::t('actions', 'Limit The Terms Of The Action')) ?>
        </div>-->
        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <?= $form
                ->field($model,'date_start')
                ->widget(DatePicker::classname(), [
                    //'options' => $model->date_start,
                    'value' => $model->date_start,
                    'removeButton' => false,
                    'pluginOptions' => ['autoclose' => true, 'format' => 'dd.mm.yyyy']])->label(Yii::t('actions', 'Date Start Action'));?>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <?= $form
                ->field($model,'date_end')
                ->widget(DatePicker::classname(), [
                    //'options' => $model->date_end,
                    'value' => $model->date_end,
                    'removeButton' => false,
                    'pluginOptions' => ['autoclose' => true, 'format' => 'dd.mm.yyyy']])->label(Yii::t('actions', 'Date End Action'));?>
        </div>
    </div>

    <?php
    if(!empty($model->file_type)){
        print Html::img(Yii::$app->params['actionsImagePath']. $model->id.'.'.$model->file_type,['width' => '300px']);
    }?>

    <?= $form->field($model, 'image')->fileInput() ?>

    <div class="row">
        <h3><?=Yii::t('actions', 'Action Params');?></h3>
        <hr style="border-style: groove;" />
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="action-param-list"><?php
                if(!empty($model->paramsValue)){
                    $actionsParamsValue = $model->paramsValue;



                foreach ($actionsParamsValue as $i => $paramsValue) {
                        $paramsName = ActionsParams::find()->where(['id'=>$paramsValue['param_id']])->asArray()->one();
                        print $this->render('_param', [
                            'paramsValue' => $paramsValue,
                            'paramsName' => $paramsName,
                            'i' => $i,
                            'disable'=>true,
                        ]);
                    }
                }?>
            </div>
            <div class="form-group">
                <?php

                $actionsParamsSelectList = \yii\helpers\ArrayHelper::map(\app\modules\actions\models\ActionsParams::find()->where(['status'=>1])->all(),'id','title');
                foreach ($actionsParamsSelectList as $k => $item) {
                    $actionsParamsSelectList[$k] = Yii::t('actions', $item);
                }
                ?>
                <?= Html::dropDownList('',0,$actionsParamsSelectList,['class'=>'form-control action-param-select'])?>

                <?= Html::button(Yii::t('actions', 'Add Param'),['class' => 'btn btn-primary add-action-param']) ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('actions', 'Create') : Yii::t('actions', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
<?php
$script = <<< JS
$(document).ready(function() {
$('.changeStatusActionParam').click(function(){
var btn = $(this);
var param_id =$(this).data('action-param-value-id');
console.log(param_id);
$.ajax({
url: "/actions/actions-params-value/change-status?id="+param_id,
success: function(data) {
if(data == 'off'){
btn.removeClass('btn-danger');
btn.addClass('btn-success');
btn.text('Включить');
}else if(data == 'on'){
btn.removeClass('btn-success');
btn.addClass('btn-danger');
btn.text('Выключить');
}
}
});
} 

);
});
JS;
$this->registerJs($script);
?>
</div>
