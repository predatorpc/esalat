<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use dosamigos\ckeditor\CKEditor;
use app\modules\managment\models\Shops;
use app\models\UserShop;
use dosamigos\tinymce\TinyMce;

/* @var $this yii\web\View */
/* @var $model app\models\Goods */
/* @var $form yii\widgets\ActiveForm */

$thisVariantComission = 25;

//\app\models\Zloradnij::printArray($model);die();
?>
<div class="shop-form" id="cms-goods">
    <div class="item">
        <div class="group">Основная информация</div>
        <?php $form = ActiveForm::begin([
            'options' => ['enctype'=>'multipart/form-data']
        ]); ?>

        <?= $form->field($model, 'type_id')->dropDownList(
            ArrayHelper::map($typeProduct, 'id', 'name')
        )->label('Тип *') ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Название *') ?>
        <?= $form->field($category, "category_id")->DropDownList(ArrayHelper::map(\app\modules\catalog\models\Category::getFullCategoriesStructureList(),'id','title'),['prompt' => 'выберите категорию'])->label('Категория'); ?>

        <?= $form->field($shopGroup, "shop_group_id")->DropDownList(ArrayHelper::map(\app\modules\managment\models\ShopGroup::find()->where(['status' => 1])->all(),'id','name'),['prompt' => 'выберите группу магазинов']);?>

        <?= $form->field($model, 'count_pack')->textInput(['maxlength' => true,'id' => 'count_pack']); ?>
        <?= $form->field($model, 'count_min')->textInput(['maxlength' => true]); ?>

        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <?= $form->field($model, 'show')->checkbox();?>
            <?= $form->field($model, 'confirm')->checkbox();?>
            <?= $form->field($model, 'autolink')->checkbox();?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <?= $form->field($model, 'status')->checkbox();?>
            <?= $form->field($model, 'bonus')->checkbox();?>
            <?= $form->field($model, 'discount')->checkbox();?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <?= $form->field($model, 'new')->checkbox();?>
            <?= $form->field($model, 'sale')->checkbox();?>
            <?= $form->field($model, 'main')->checkbox();?>
        </div>

        <?= $form->field($model, 'position')->textInput(['maxlength' => true])->label('Сортировка'); ?>

        <?= $form->field($model, 'description')->widget(TinyMce::className(), [
            'options' => ['rows' => 6],
        ]) ?>
        <?= $form->field($model, 'seo_title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'seo_keywords')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'seo_description')->textInput(['maxlength' => true]) ?>

<!--        <div class="form-group field-goods-country_id has-success">-->
<!--            <label class="control-label" for="goods-country_id">Страна</label>-->
<!--            <select id="goods-country_id" class="form-control" name="country-all">-->
<!--                --><?php
//                $tagsProducerListId = false;
//                if(is_array($modelVariant) && isset($tagsListValue[$modelVariant[0]->id][1007])){
//                    foreach($tagsListValue[$modelVariant[0]->id][1007] as $prodId => $prodName){
//                        $tagsProducerListId = $prodId;
//                    }
//                }
//                foreach($country as $item){
//                    $selected = '';
//                    if($tagsProducerListId == $item->id){
//                        $selected = ' selected';
//                    }
//                    print '
//                            <option value="'.$item->id.'" '.$selected.'>'.$item->value.'</option>
//                            ';
//                }
//                ?>
<!--            </select>-->
<!--        </div>-->

        <?php
        if(isset($modelVariant[0])){
            print '
                <div class="group">Варианты</div>
                ';
            foreach($modelVariant as $key => $variant){
                if(isset($variant->comission) && $variant->comission > 0){
                    $thisVariantComission = $variant->comission;
                }
                ?>
                <div class="variants" data-key="<?=$key?>">
                    <div class="variant-title">
                        <?= $variant->id ?> -
                        <?= ($variant->full_name)?$variant->full_name:$model->name ?>
                        <?=(isset($variant->tags_name))?' - '.$variant->tags_name:''?>
                    </div>
                    <div class="variant-body close">
                        <?= $form->field($variant, "[$key]id")->hiddenInput()->label('') ?>
                        <?= $form->field($variant, "[$key]full_name")->textInput(['maxlength' => true]) ?>
                        <?= $form->field($variant, "[$key]code")->textInput(['maxlength' => true]) ?>
                        <?= $form->field($variant, "[$key]price")->textInput(['maxlength' => true,'class' => 'form-control price_in'])->label('Цена входная *') ?>
                        <?= $form->field($variant, "[$key]comission")->textInput(['maxlength' => true,'class' => 'form-control comission money'])->label('Комиссия *') ?>

                        <div class="form-group has-success">
                            <label class="control-label" for="goodsvariations-0-price">Цена выходная</label>
                            <input id="goodsvariations-0-price-output" class="form-control price_out" type="text" value="<?=round($variant->price*(1+$variant->comission/100),2)?>" name="">
                            <div class="help-block"></div>
                        </div>

                        <?php
                        if($shopStoreCount && isset($shopsList) && !empty($shopsList)){
                            foreach($shopsList as $item){
//                            print '--';
//                            Zloradnij::printArray($this->shopStoreCount[$variantIdStatic]);die();?>
                                <?= $form->field($shopStoreCount[$variant->id][$item->id], "[".$variant->id."][$item->id]id")->hiddenInput()->label(''); ?>
                                <?= $form
                                    ->field($shopStoreCount[$variant->id][$item->id], "[".$variant->id."][$item->id]count")
                                    ->textInput(['maxlength' => true])
                                    ->label($item->getShopNameStringTitle().'<br />'.$item->name.'<br />'.$item->getAddressStringTitle()); ?>
                                <?php
                            }
                        }else{

                        }?>

                        <!-- add tags -->
                        <div class="variation">
                            <?php
                            foreach($tagsList as $k => $tagItem){
                                $oldTagsVariantsString = '';
                                if(isset($tagsListValue[$variant->id][$tagItem->id])){
                                    $oldTagsVariantsString = '<span class="tag">';
                                    foreach($tagsListValue[$variant->id][$tagItem->id] as $idTag => $valTag){
                                        $oldTagsVariantsString .= '
                                            <input type="hidden" value="Natural" name="variations_add['.$key.'][tags]['.$idTag.']">
                                            '.$valTag.'
                                            <a onclick="$(this).parent().remove(); return false;" title="Удалить тег" href="/">X</a>
                                            ';
                                    }
                                    $oldTagsVariantsString .= '</span>';
                                }
                                print '
                                    <div class="form-group field-tagsgroups-name i options">
                                        <label class="control-label" for="tagsgroups-name">'.$tagItem->name.'</label>
                                        <div class="value value-'.$tagItem->id.'">
                                            <input type="text" name="" value="" maxlength="64" group="'.$tagItem->id.'" class="string form-control">
                                            <div class="load"></div>
                                            <div class="values"></div>
                                            '.$oldTagsVariantsString.'
                                        </div>
                                    </div>
                                    ';
                            }
                            ?>
                        </div>
                        <div class="images row general-image-variant-block" data-variant="<?=$variant->id?>">
                            <?php
                            $images[$variant->id][] = new \app\modules\catalog\models\GoodsImages();
                            foreach($images[$variant->id] as $image){
                                if(isset($image->id) && !empty($image->id)){?>
                                    <div style="display:inline-block;position: relative;">
                                    <img src="/files/goods/<?= \app\modules\catalog\models\GoodsImages::getImageFolder($image->id).'/'.$image->id?>.jpg" style="width:150px;" />
                                    <span
                                        class="delete-image-block-new"
                                        style="position: absolute;display: block;top:0;right:0;cursor: pointer;padding:5px 10px;"
                                        data-product="<?= $model->id?>"
                                        data-variant="<?= $variant->id?>"
                                        data-image="<?= $image->id?>"
                                    >X</span>
                                    </div><?php
                                }
                            }
                            $j = 0;
                            print $form->field($image, "[".$variant->id."][$j]id")->fileInput()->hint('Загрузить фото товара')->label('');
                            ?>
                        </div>
                        <!-- END >> add tags -->

                        <?= $form->field($variant, "[$key]status")->checkbox(['value' => 1,'label' => 'Активность']) ?>

                    </div>
                </div>
                <hr />
                <?php
            }
        }
        ?>

        <div class="blockForNewVariantsForms"></div>
        <div class="form-group variantFormAddThis">
            <span class="btn btn-primary addVariantFormForProvider">Добавить вариант</span>
        </div>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Создать товар' : 'Обновить товар', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php $form->end(); ?>
    </div>
</div>
<!-- Modal "" -->
<div class="modal fade" id="select-image-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                ...
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

