<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\UserShop;
use dosamigos\tinymce\TinyMce;
use kartik\widgets\ColorInput;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Goods */
/* @var $form yii\widgets\ActiveForm */


$thisVariantComission = 25;

//\app\models\Zloradnij::printArray($model);die();
?>
<div style="padding: 15px 0">
    <b><?=Yii::t('admin','Навигация')?></b><br>
    <?php foreach($menu['items'] as $key=>$item): ?>
        <a style="margin: 0 15px 0 0px;" href="<?=$item['link']?>"><?=Yii::t('admin',$item['title'])?></a>
    <?php endforeach; ?>
</div>
<div class="shop-form" id="cms-goods">
    <p>
        <?= Html::a(Yii::t('admin', 'Копировать карточку товара'), ['/product/copy','id'=>$model->id], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="item content-good">
        <div class="group"><?= Yii::t('admin', 'Основная информация') ?></div>
        <?php $form = ActiveForm::begin([
            'options' => ['enctype'=>'multipart/form-data']
        ]); ?>
        <?= $form->field($model, "id")->textInput(['readonly' => true]); ?>
        <?= $form->field($model, 'type_id')->dropDownList(
            ArrayHelper::map($typeProduct, 'id', 'name')
        )->label(Yii::t('admin', 'Тип *')) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label(Yii::t('admin', 'Название *')) ?>
        <?= $form->field($category, "category_id")->DropDownList(ArrayHelper::map(\app\modules\catalog\models\Category::getFullCategoriesStructureList(),'id','title'),['prompt' => Yii::t('admin', 'выберите категорию')])->label(Yii::t('admin', 'Категория')); ?>

        <?= $form->field($shopGroup, "shop_group_id")->DropDownList(ArrayHelper::map(\app\modules\managment\models\ShopGroup::find()->where(['status' => 1])->all(),'id','name'),['prompt' => Yii::t('admin', 'выберите группу магазинов')]);?>

        <?= $form->field($model, 'count_pack')->textInput(['maxlength' => true,'id' => 'count_pack']); ?>
        <?= $form->field($model, 'count_min')->textInput(['maxlength' => true]); ?>

        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <?= $form->field($model, 'show')->checkbox()?>
            <?= $form->field($model, 'confirm')->checkbox()?>
            <?= $form->field($model, 'autolink')->checkbox()?>
            <?= $form->field($model, 'hit')->checkbox()?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <?= $form->field($model, 'status')->checkbox()?>
            <?= $form->field($model, 'bonus')->checkbox()?>
            <?= $form->field($model, 'discount')->checkbox()?>
            <?= $form->field($model, 'type')->checkbox(['value' => 1,'label' => Yii::t('admin', 'Большая карточка')])?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
            <?= $form->field($model, 'new')->checkbox()?>
            <?= $form->field($model, 'sale')->checkbox()?>
            <?= $form->field($model, 'main')->checkbox()?>
            <?= $form->field($model, "master_active")->checkbox(['value' => 1,'label' => Yii::t('admin', 'Мастер покупки')]) ?>
            <?= $form->field($model, 'iwish')->checkbox();?>

        </div>

        <label class="control-label" for="shops-id">Стикеры</label>
        <?= Select2::widget([
            'name' => 'sticker',
            'data' => ArrayHelper::map(\app\modules\catalog\models\Sticker::find()->where(['status'=>1])->all(),'id','name'),
            'value' => ArrayHelper::getColumn(\app\modules\catalog\models\StickerLinks::find()->where(['good_id'=>$model->id,'status'=>1])->All(),'sticker_id'),
            'options' => [
                'placeholder' => 'Выбирите стикеры..',
                'multiple' => true
            ],
        ]);?>

        <?= $form->field($model, 'position')->textInput(['maxlength' => true])->label(Yii::t('admin', 'Сортировка')); ?>

        <?=$form->field($model, 'color_bg')->widget(ColorInput::classname(), [
            'options' => [
                'placeholder' => 'Select color ...',
            ],
        ]);?>


        <?= $form->field($model, 'description')->widget(TinyMce::className(), [
            'options' => ['rows' => 6],
            'language' => 'ru',
            'clientOptions'=>[
              //  'plugins' => 'lists',
                'plugins'=> [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc'
                ],
                'toolbar'=>'undo redo | stylesheet | bold italic | alignleft aligncenter alignright alignjustify | bullist  numlist|print preview media'
            ],
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
                <div class="group">'.Yii::t('admin', 'Варианты').'</div>
                ';
            foreach($modelVariant as $key => $variant){
                if(isset($variant->comission) && $variant->comission > 0){
                    $thisVariantComission = $variant->comission;
                }
                ?>
                <div class="variants" data-key="<?=$key?>">
                    <div class="variant-title">
                        <?php
                        if ($variant->status == 1){?>
                            <span class="btn-success"><?php
                        }else{?>
                            <span class="btn-danger"><?php
                        }
                        ?>
                        <?= $variant->id ?> -
                        <?= ($variant->full_name)?$variant->full_name:$model->name ?>
                        <?=(isset($variant->tags_name))?' - '.$variant->tags_name:''?>
                        </span>
                    </div>
                    <div class="variant-body close">
                        <?= $form->field($variant, "[$key]id")->hiddenInput()->label('') ?>
                        <?= $form->field($variant, "[$key]full_name")->textInput(['maxlength' => true]) ?>
                        <?= $form->field($variant, "[$key]code")->textInput(['maxlength' => true]) ?>
                        <?= $form->field($variant, "[$key]servingforday")->textInput(['maxlength' => true]) ?>
                        <?= $form->field($variant, "[$key]price")->textInput(['maxlength' => true,'class' => 'form-control price_in'])->label(Yii::t('admin','Цена входная').' *') ?>
                        <?= $form->field($variant, "[$key]comission")->textInput(['maxlength' => true,'class' => 'form-control comission money'])->label(Yii::t('admin','Комиссия').' *') ?>

                        <div class="form-group has-success">
                            <label class="control-label" for="goodsvariations-0-price"><?= Yii::t('admin', 'Цена выходная') ?></label>
                            <input id="goodsvariations-0-price-output" class="form-control price_out" type="text" value="<?=round($variant->price*(1+$variant->comission/100),2)?>" name="">
                            <div class="help-block"></div>
                        </div>

                        <?php
                        if($shopStoreCount && isset($shopsList) && !empty($shopsList)){
                            foreach($shopsList as $item){
//                            print '--';
//                            Zloradnij::printArray($this->shopStoreCount[$variantIdStatic]);die();?>
                                <?php
                                if(empty($shopStoreCount[$variant->id][$item->id])){
                                    $shopStoreCount[$variant->id][$item->id] = new \app\modules\catalog\models\GoodsCounts();
                                }

                                ?>
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
                                            <a onclick="$(this).parent().remove(); return false;" title="'.Yii::t('admin', 'Удалить тег').'" href="/">X</a>
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
                        <!-- Загрузка картина-->
                        <div class="images general-image-variant-block" data-variant="<?=$variant->id?>">

                            <?php

                            $images[$variant->id][] = new \app\modules\catalog\models\GoodsImages();?>
                            <div class="images-all images_variation-<?=$variant->id?>">
                               <?= \app\components\shopProducts\WImages::widget(['images'=>$images,'model'=>$model,'variant_id'=>$variant->id]);?>
                            </div>
                            <?php $image = array_pop($images[$variant->id]);

                            $j = 0;
                            /*
                            echo $form->field($image, "[".$variant->id."][$j]id[]")->widget(FileInput::classname(), [
                                'options' => ['multiple' => true],
                            ])->label(Yii::t('admin', 'Загрузить фото товара'));*/

                         echo   $form->field($image, "[".$variant->id."][$j]id",['enableClientValidation' => false])->widget(
                                FileInput::classname(), [
                                    'options' => ['multiple' => true],
                                 'language' => 'ru',
                                    'pluginOptions' => ['previewFileType' => 'image',
                                        'uploadUrl' => '/product/update?id='.$model->id,
                                        'uploadExtraData' => [
                                            'imagesAjax' => true,
                                            'good_id_a' => $model->id,
                                            'variant_id_a' => $variant->id,
                                        ],
                                        'maxFileCount' => 6
                                    ],
                                    'pluginEvents' => [
                                        "fileuploaded" => "function(event, data, previewId, index) {
                      var response = data.response;
                      if(response.success) {
                          $.post('/product/update?id={$model->id}',{'imagesAjaxUpdate':true,'variant_id':".$variant->id." },function(html){
                              $(\"div.images_variation-{$variant->id}\").html(html);
                          });
                      }
                 }",
                                    ],
                                ]
                            )->label(Yii::t('admin', 'Загрузить фото товара'));


                            ?>

                        </div> <!-- Загрузка картина-->
                        <!-- END >> add tags -->

                        <?= $form->field($variant, "[$key]status")->checkbox(['value' => 1,'label' => Yii::t('admin', 'Активность')]) ?>

                    </div>
                </div>
                <hr />
                <?php
            }
        }
        ?>

        <div class="blockForNewVariantsForms"></div>
        <div class="form-group variantFormAddThis">
            <span class="btn btn-primary addVariantFormForProvider"><?= Yii::t('admin', 'Добавить вариант') ?></span>
        </div>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Создать товар') : Yii::t('admin', 'Обновить товар'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <button type="button" onclick="return show_modal_compact('/product/review','Просмотреть','<?=$model->id?>');" class="btn btn-info review">Просмотреть</button>
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

