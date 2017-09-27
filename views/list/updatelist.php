<?php


use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>

<div class="lists-form">

<?php $form = ActiveForm::begin([
    'action' => 'updatelistitem',
    'options' => ['enctype'=>'multipart/form-data']
]); ?>

    <h3><?php
        $q = \app\modules\catalog\models\GoodsVariations::findOne($model->variation_id);
            echo $q->getTitleWithProperties();?></h3>
    <?php  echo Html::hiddenInput('modelId', $modelId); ?>
    <?= $form->field($model, 'id')->textInput(['readonly' => true]) ?>
    <?= $form->field($model, 'variation_id')->textInput(['readonly' => true]) ?>
    <?= $form->field($model, 'amount')->textInput(['readonly' => false]) ?>
    <?= $form->field($model, 'sort')->textInput(['readonly' => false]) ?>
    <?php //= $form->field($model, 'status')->checkbox() ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('admin','Обновить'),['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    <?= Html::a('Назад','/list/update?id='.$modelId,['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>


<?php ActiveForm::end(); ?>

</div>
