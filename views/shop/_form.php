<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use dosamigos\ckeditor\CKEditor;
use app\models\Shops;
use app\models\UserShop;

/* @var $this yii\web\View */
/* @var $model app\models\Goods */
/* @var $form yii\widgets\ActiveForm */

$shopId = UserShop::getIdentityShop();
$shop = Shops::find()->where(['id' => $shopId])->one();
$thisVariantComission = $shop->comission_value;
?>
<div class="shop-form" id="cms-goods">
    <div class="item">
        <div class="group">Основная информация</div>
        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'type_id')->dropDownList(
                ArrayHelper::map($typeProduct, 'id', 'name')
            )->label('Тип *') ?>
            <input type="hidden" name="Goods[shop_id]" value="<?=$shopId?>">

        <?php
        /*
        = $form->field($model, 'producer_id')->dropDownList(
                ArrayHelper::map($producers, 'id', 'value')
            ) */
        ?>
            <div class="form-group field-goods-producer_id has-success">
                <label class="control-label" for="goods-producer_id">Производитель</label>
                <select id="goods-producer_id" class="form-control" name="producer-all">
                    <?php
                    $tagsProducerListId = false;
                    if(is_array($modelVariant) && isset($tagsListValue[$modelVariant[0]->id][1008])){
                        foreach($tagsListValue[$modelVariant[0]->id][1008] as $prodId => $prodName){
                            $tagsProducerListId = $prodId;
                        }
                    }
                    foreach($producers as $item){
                        $selected = '';
                        if($tagsProducerListId == $item->id){
                            $selected = ' selected';
                        }
                        print '
                        <option value="'.$item->id.'" '.$selected.'>'.$item->value.'</option>
                        ';
                    }
                    ?>
                </select>
            </div>

            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Название *') ?>

            <?= $form->field($model, 'description')->widget(CKEditor::className(), [
                'options' => ['rows' => 6],
                'preset' => 'basic'
            ]) ?>
            <div class="hide">
                <?= $form->field($model, 'count_pack')->textInput(['maxlength' => true,'id' => 'count_pack'])->label('Кол-во в упаковке *') ?>
            </div>
            <div class="hide">
                <input id="comission_id" type="hidden" value="<?=$shop->comission_id?>">
            </div>

            <?php
            /*
            $form->field($model, 'country_id')->dropDownList(
                ArrayHelper::map($country, 'id', 'name')
            )
            */
            ?>
            <div class="form-group field-goods-country_id has-success">
                <label class="control-label" for="goods-country_id">Страна</label>
                <select id="goods-country_id" class="form-control" name="country-all">
                    <?php
                    $tagsProducerListId = false;
                    if(is_array($modelVariant) && isset($tagsListValue[$modelVariant[0]->id][1007])){
                        foreach($tagsListValue[$modelVariant[0]->id][1007] as $prodId => $prodName){
                            $tagsProducerListId = $prodId;
                        }
                    }
                    foreach($country as $item){
                        $selected = '';
                        if($tagsProducerListId == $item->id){
                            $selected = ' selected';
                        }
                        print '
                            <option value="'.$item->id.'" '.$selected.'>'.$item->value.'</option>
                            ';
                    }
                    ?>
                </select>
            </div>

            <?= $form->field($model, 'status')->checkbox(['value' => 1, 'label' => 'Отображать на сайте']) ?>

            <?php
            if(isset($modelVariant[0])){
                print '
                <div class="group">Варианты</div>
                ';
                foreach($modelVariant as $key => $variant){
                    if(isset($variant->comission) && $variant->comission > 0){
                        $thisVariantComission = $variant->comission;
                    }
                    //print '<pre style="display: none;">';print_r($variant);print '</pre>';
                ?>
                    <div class="variants" data-key="<?=$key?>">
                        <div class="variant-title">
                            <?= ($variant->full_name)?$variant->full_name:$model->name ?>
                            <?=(isset($variant->tags_name))?' - '.$variant->tags_name:''?>
                        </div>
                        <div class="variant-body close">
                            <?= $form->field($variant, "[$key]id")->hiddenInput()->label('') ?>
                            <?= $form->field($variant, "[$key]full_name")->textInput(['maxlength' => true]) ?>
                            <?= $form->field($variant, "[$key]code")->textInput(['maxlength' => true]) ?>
                            <?= $form->field($variant, "[$key]price")->textInput(['maxlength' => true,'class' => 'form-control price_in'])->label('Цена входная *') ?>

                            <div class="form-group has-success">
                                <label class="control-label" for="goodsvariations-0-price">Цена выходная</label>
                                <input id="goodsvariations-0-price-output" class="form-control price_out" type="text" value="<?=round($variant->price*(1+$variant->comission/100),2)?>" name="">
                                <div class="help-block"></div>
                            </div>

                            <div class="hide">
                                <?= $form->field($variant, "[$key]comission")->textInput(['maxlength' => true,'class' => 'comission money','value' => $thisVariantComission])->label('Комиссия *') ?>
                            </div>


                            <?php
                            $variant->count = 0;
                            if(isset($countVariation[$variant->id])){
                                $variant->count = $countVariation[$variant->id]['count'];
                            }
                            ?>
                            <?= $form->field($variant, "[$key]count"/*,['value' => $countVariation[$variant->id]*/)->input('number') ?>

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
                                if(isset($variantImages[$variant->id]) && !empty($variantImages[$variant->id])){
                                    foreach($variantImages[$variant->id] as $imageId => $imagePath){
                                        print '
                                        <div data-image="'.$imageId.'" class="col-xs-4 col-sm-3 col-md-3 col-lg-2 gallery-image-container">
                                            <img src="'.$imagePath.'" />
                                            <div class="modal-gallery-buttons" data-product="'.$model->id.'" data-variant="'.$variant->id.'" data-image="'.$imageId.'">
                                                <span class="remove">Убрать</span>
                                            </div>
                                            <span class="modal-gallery-check-image">V</span>
                                        </div>
                                        ';
                                    }
                                }
                                ?>
                            </div>
                            <!-- END >> add tags -->

                            <?= $form->field($variant, "[$key]status")->checkbox(['value' => 1,'label' => 'Активность']) ?>
                            <div class="">
                                <span class="variantImages btn btn-primary addVariantImage" data-product="<?=$model->id?>" data-variant="<?=$variant->id?>">Добавить фото</span>
                            </div>

                        </div>
                    </div>
                <?php
                }
            }else{
                print '
                <div class="group">Добавить вариант</div>
                ';
                ?>
                <div class="variants" data-key="0">
                    <?= $form->field($modelVariant, '[0]full_name')->textInput() ?>
                    <?= $form->field($modelVariant, '[0]code')->textInput() ?>

                    <?= $form->field($modelVariant, '[0]price')->textInput(['maxlength' => true,'class' => 'form-control price_in'])->label('Цена входная *') ?>
                    <div class="form-group has-success">
                        <label class="control-label" for="goodsvariations-0-price">Цена выходная</label>
                        <input id="goodsvariations-0-price-output" class="form-control price_out" type="text" value="0" name="">
                        <div class="help-block"></div>
                    </div>

                    <div class="hide">
                        <?= $form->field($modelVariant, "[0]comission")->textInput(['maxlength' => true,'class' => 'comission money','value' => $thisVariantComission])->label('Комиссия *') ?>
                    </div>

                    <?= $form->field($modelVariant, '[0]count')->input('number') ?>

                    <!-- add tags -->
                    <div class="variation">
                        <?php
                        foreach($tagsList as $k => $tagItem){
                            print '
                            <div class="form-group field-tagsgroups-name i options">
                                <label class="control-label" for="tagsgroups-name">'.$tagItem->name.'</label>
                                <div class="value value-'.$tagItem->id.'">
                                    <input type="text" name="" value="" maxlength="64" group="'.$tagItem->id.'" class="string form-control">
                                    <div class="load"></div>
                                    <div class="values"></div>
                                </div>
                            </div>
                            ';
                        }
                        ?>
                    </div>
                    <!-- END >> add tags -->

                    <?= $form->field($modelVariant, '[0]status')->checkbox(['value' => 1, 'label' => 'Активность']) ?>
                </div>
                <?php
            }
            ?>

            <div class="blockForNewVariantsForms"></div>
            <div class="form-group variantFormAddThis">
                <span class="btn btn-primary addVariantFormForProvider">Добавить вариант</span>
            </div>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Создать товар' : 'Обновить товар', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

        <?php ActiveForm::end(); ?>
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

