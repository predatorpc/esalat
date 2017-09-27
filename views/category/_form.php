<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
use kartik\widgets\ColorInput;
use mihaildev\ckeditor\CKEditor;
/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="category-form">


    <?php $form = ActiveForm::begin(); ?>

    <?=$form->field($model, 'parent_id')->DropDownList(ArrayHelper::map(array_merge(['id' => 0,'title' => '--'],$parent),'id','title'));  ?>

    <?=$form->field($model, 'level')->textInput(['readonly' => true]); ?>

    <?=$form->field($model, 'title')->textInput(['maxlength' => true])->label(Yii::t('admin','Название категории (Title)')); ?>


    <?= $form->field($model, 'description')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'editorOptions' => [
            'preset' => 'standard', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
            'inline' => false, //по умолчанию false
        ],

    ]) ?>
    <!-- Шаблон для алиаса использует кастомизированный /framework/vendor/bouer/query/qjuery.inputmask.bundle.js -->
    <?php //if($model->isNewRecord): ?>
    <?=$form->field($model, 'alias')->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', ])
        ->label(Yii::t('admin','Адрес в браузере (Alias)'))->hint(Yii::t('admin','Например: http://www.Esalad.ru/catalog/foods/suhofrukti-i-orehi, В данном контексте suhofrukti-i-orehi является Alias-ом'));  ?>
     <?php //endif; ?>
    <!-- SEO Section -->

    <?=$form->field($model, 'anon')->textarea(['rows' => 6]);  ?>

    <?=$form->field($model, 'seo_title')->textInput(['maxlength' => true]); ?>

    <?=$form->field($model, 'seo_description')->textarea(['rows' => 6]);  ?>

    <?=$form->field($model, 'seo_keywords')->textInput(['maxlength' => true]);  ?>

    <!-- SEO Section -->

    <?=$form->field($model, 'sort')->textInput();  ?>

    <?php /*= $form->field($model, 'vegan')
        ->checkbox([
            'label' => 'Для вегетерианцев',
            'labelOptions' => [
                'style' => 'padding-left:20px;'
            ],
            'disabled' => false,
        ]);*/
    ?>

    <?=$form->field($model, 'color')->widget(ColorInput::classname(), [
        'options' => [
            'placeholder' => 'Select color ...',
        ],
    ]);?>
    <?= $form->field($model, 'type_master')
        ->checkbox([
            'label' => Yii::t('admin','вкл./выкл Мастер покупки'),
            'labelOptions' => [
                'style' => 'padding-left:20px;'
            ],
            'disabled' => false,
        ]);
    ?>
    
    <?php // if(Yii::$app->user->can('GodMode')){ ?>
    
    <?= $form->field($model, 'active')
        ->checkbox([
            'label' => Yii::t('admin','Активная категория'),
            'labelOptions' => [
                'style' => 'padding-left:20px;'
            ],
            'disabled' => false,
        ]);
    ?>
    <?php// }?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin','Создать') : Yii::t('admin','Обновить'), ['class' =>  'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
