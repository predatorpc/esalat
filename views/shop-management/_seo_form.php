<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if($catMgr==true){echo $form->field($model, 'parent_id')->DropDownList(ArrayHelper::map(array_merge(['id' => 0,'title' => '--'],$parent),'id','title')); } ?>

    <?php if($catMgr==true){echo  $form->field($model, 'level')->textInput(['readonly' => true]); }?>

    <?php if($catMgr==true){echo  $form->field($model, 'title')->textInput(['maxlength' => true])->label(Yii::t('admin','Название категории (Title)')); }?>

    <?php if($catMgr==true || $seoMgr==true){echo  $form->field($model, 'description')->textarea(['rows' => 6]); } ?>

    <!-- Шаблон для алиаса использует кастомизированный /framework/vendor/bouer/query/qjuery.inputmask.bundle.js -->

    <?php if($catMgr==true){echo $form->field($model, 'alias')->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', ])
        ->label(Yii::t('admin', 'Адрес в браузере (Alias)'))->hint(Yii::t('admin', 'Например: http://www.Esalad.ru/catalog/foods/suhofrukti-i-orehi, В данном контексте suhofrukti-i-orehi является Alias-ом')); } ?>

    <!-- SEO Section -->

    <?php if($seoMgr==true){echo $form->field($model, 'seo_title')->textInput(['maxlength' => true]); }?>

    <?php if($seoMgr==true){echo $form->field($model, 'seo_description')->textarea(['rows' => 6]); } ?>

    <?php if($seoMgr==true){echo $form->field($model, 'seo_keywords')->textInput(['maxlength' => true]); } ?>

    <!-- SEO Section -->

    <?php if($catMgr==true){echo $form->field($model, 'sort')->textInput(); } ?>

    <?php if($catMgr==true){echo

    $form->field($model, 'active')
        ->checkbox([
            'label' => Yii::t('admin', 'Активная категория'),
            'labelOptions' => [
                'style' => 'padding-left:20px;'
            ],
            'disabled' => false,
        ]);

    }else {
        $disabled = false;
        if ($model->active == 1) {
            $disabled = false;
        } else {
            $disabled = true;
        }

        $form->field($model, 'active')
            ->checkbox(
                [
                    'label'        => Yii::t('admin', 'Активная категория'),
                    'labelOptions' => [
                        'style' => 'padding-left:20px;'
                    ],
                    'disabled'     => $disabled,
                ]
            );

    }

    //$model->isNewRecord ? 'btn btn-success' :
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Создать') : Yii::t('admin', 'Обновить'), ['class' =>  'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
