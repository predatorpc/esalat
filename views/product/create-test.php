<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


//\app\models\Zloradnij::printArray($product);
//\app\models\Zloradnij::printArray($categoryLinks);
//\app\models\Zloradnij::printArray($shopGroupVariantLink);
//\app\models\Zloradnij::printArray($productVariation);

$currentShopGroupComissionId = $shopGroup ? $shopGroup->comission_id : 1001;

?>
<?php $form = ActiveForm::begin([
    'options' => ['enctype'=>'multipart/form-data']
]); ?>
    <?= $form->field($product, "uniqueHashString")->hiddenInput()->label(''); ?>
    <?= $form->field($product, 'type_id')->dropDownList(
        ArrayHelper::map(\app\models\GoodsTypes::find()->where(['status' => 1])->all(), 'id', 'name'),['prompt' => Yii::t('admin','выберите тип товара')]
    )->label(Yii::t('admin','Тип').' *') ?>

    <?= $form->field($product, 'name')->textInput(['maxlength' => true])->label(Yii::t('admin','Название').' *') ?>
    <?= $form->field($categoryLinks, "category_id")->DropDownList(ArrayHelper::map(\app\models\Category::getFullCategoriesStructureList(),'id','title'),['prompt' => Yii::t('admin','выберите категорию')])->label(Yii::t('admin','Категория')); ?>

    <?= $form->field($shopGroupVariantLink, "shop_group_id")->DropDownList(ArrayHelper::map(\app\models\ShopGroup::find()->where(['status' => 1])->all(),'id','name'),['prompt' => Yii::t('admin','выберите группу магазинов')]);?>

    <?= $form->field($product, 'count_pack')->textInput(['maxlength' => true,'id' => 'count_pack']); ?>
    <?= $form->field($product, 'count_min')->textInput(['maxlength' => true]); ?>

    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <?= $form->field($product, 'show')->checkbox();?>
        <?= $form->field($product, 'confirm')->checkbox();?>
        <?= $form->field($product, 'autolink')->checkbox();?>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <?= $form->field($product, 'status')->checkbox();?>
        <?= $form->field($product, 'bonus')->checkbox();?>
        <?= $form->field($product, 'discount')->checkbox();?>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <?= $form->field($product, 'new')->checkbox();?>
        <?= $form->field($product, 'sale')->checkbox();?>
        <?= $form->field($product, 'main')->checkbox();?>
    </div>

    <?= $form->field($product, 'position')->textInput(['maxlength' => true])->label(Yii::t('admin','Сортировка')); ?>

    <?= $form->field($product, 'description')->widget(\dosamigos\tinymce\TinyMce::className(), [
        'options' => ['rows' => 6],
    ]) ?>

    <?= $form->field($product, 'seo_title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($product, 'seo_keywords')->textInput(['maxlength' => true]) ?>
    <?= $form->field($product, 'seo_description')->textInput(['maxlength' => true]) ?>


    <?php
//$a = \app\models\ShopGeneral::checkVariantCountQuery(10401835,1000054702);
\app\models\Zloradnij::printArray($productCountInStores);

    foreach($productVariation as $key => $variant){
        $variantTitle = $variant->id ? $variant->id : '';
        $variantTitle .= isset($variant->full_name) ? ' - ' . $variant->full_name : '';
        $variantTitle .= $product->name ? ' - ' . $product->name : '';
        $variantTitle .= isset($variant->tags_name) ? ' - ' . $variant->tags_name : Yii::t('admin','Новая вариация');
        ?>
        <div class="variants" data-key="<?=$key?>">
            <div class="variant-title">
                <?= $variantTitle?>
            </div>
            <div class="variant-body close">
                <?= $form->field($variant, "[".$variant->uniqueHashString."]uniqueHashParentString")->hiddenInput()->label(''); ?>
                <?= $form->field($variant, "[".$variant->uniqueHashString."]uniqueHashString")->hiddenInput()->label(''); ?>
                <?= $form->field($variant, "[".$variant->uniqueHashString."]id")->hiddenInput()->label('') ?>
                <?= $form->field($variant, "[".$variant->uniqueHashString."]full_name")->textInput(['maxlength' => true]) ?>
                <?= $form->field($variant, "[".$variant->uniqueHashString."]code")->textInput(['maxlength' => true]) ?>
                <?= $form->field($variant, "[".$variant->uniqueHashString."]price")->textInput(['maxlength' => true,'class' => 'form-control price_in'])->label(Yii::t('admin','Цена входная').' *') ?>
                <?= $form->field($variant, "[".$variant->uniqueHashString."]comission")->textInput(['maxlength' => true,'class' => 'form-control comission money'])->label(Yii::t('admin','Комиссия').' *') ?>

                <div class="form-group has-success">
                    <label class="control-label" for="goodsvariations-0-price"><?=Yii::t('admin','Цена выходная')?></label>
                    <input id="goodsvariations-0-price-output" class="form-control price_out" type="text" value="<?=round($variant->price*(1+$variant->comission/100),2)?>" name="">
                    <div class="help-block"></div>
                </div>

                <div class="storesBlock">
                <?php
                if(!$productStores){

                }else{
                    foreach($productStores as $storeItem){?>
                        <?= Html::hiddenInput("ShopsStores[".$storeItem->uniqueHashString."][id]", $storeItem->id) ?>
                        <?= Html::hiddenInput("ShopsStores[".$storeItem->uniqueHashString."][shop_id]", $storeItem->shop_id) ?>
                        <?= Html::hiddenInput("ShopsStores[".$storeItem->uniqueHashString."][uniqueHashString]", $storeItem->uniqueHashString) ?>

                        <?php
                        $zeroProductCount = new \app\models\GoodsCounts();
                        $zeroProductCount->uniqueHashParentString = $variant->uniqueHashString;
                        $zeroProductCount->count = isset($productCountInStores[$variant->id][$storeItem->id]) ? $productCountInStores[$variant->id][$storeItem->id] : 0;
                        if(isset($productCountInStores[$variant->id][$storeItem->id])){?>
                            <?= Html::hiddenInput("GoodsCounts[$variant->uniqueHashString][$storeItem->uniqueHashString][uniqueHashParentString]", $storeItem->uniqueHashString) ?>
                            <?= Html::hiddenInput("GoodsCounts[$variant->uniqueHashString][$storeItem->uniqueHashString][uniqueHashString]", $zeroProductCount->uniqueHashString) ?>
                            <?= $form
                                ->field($zeroProductCount, "[$variant->uniqueHashString][$storeItem->uniqueHashString]count")
                                ->textInput(['maxlength' => true])
                                ->label($storeItem->name.'<br />'.$storeItem->address);?>
                            <?php
                        }
                    }
                }?>
                </div>

                <!-- add tags -->
                <div class="variation">
                    <?php
//                    foreach($tagsList as $k => $tagItem){
//                        $oldTagsVariantsString = '';
//                        if(isset($tagsListValue[$variant->id][$tagItem->id])){
//                            $oldTagsVariantsString = '<span class="tag">';
//                            foreach($tagsListValue[$variant->id][$tagItem->id] as $idTag => $valTag){
//                                $oldTagsVariantsString .= '
//                                                <input type="hidden" value="Natural" name="variations_add['.$key.'][tags]['.$idTag.']">
//                                                '.$valTag.'
//                                                <a onclick="$(this).parent().remove(); return false;" title="Удалить тег" href="/">X</a>
//                                                ';
//                            }
//                            $oldTagsVariantsString .= '</span>';
//                        }
//                        print '
//                                        <div class="form-group field-tagsgroups-name i options">
//                                            <label class="control-label" for="tagsgroups-name">'.$tagItem->name.'</label>
//                                            <div class="value value-'.$tagItem->id.'">
//                                                <input type="text" name="" value="" maxlength="64" group="'.$tagItem->id.'" class="string form-control">
//                                                <div class="load"></div>
//                                                <div class="values"></div>
//                                                '.$oldTagsVariantsString.'
//                                            </div>
//                                        </div>
//                                        ';
//                    }
                    ?>
                </div>
                <div class="images row general-image-variant-block" data-variant="<?=$variant->id?>">
                    <?php
//                    $images[$variant->id][] = new \app\models\GoodsImages();
//                    foreach($images[$variant->id] as $image){
//                        if(isset($image->id) && !empty($image->id)){?>
<!--                            <div style="display:inline-block;position: relative;">-->
<!--                            <img src="/files/goods/--><?//= \app\models\GoodsImages::getImageFolder($image->id).'/'.$image->id?><!--.jpg" style="width:150px;" />-->
<!--                                        <span-->
<!--                                            class="delete-image-block-new"-->
<!--                                            style="position: absolute;display: block;top:0;right:0;cursor: pointer;padding:5px 10px;"-->
<!--                                            data-product="--><?//= $model->id?><!--"-->
<!--                                            data-variant="--><?//= $variant->id?><!--"-->
<!--                                            data-image="--><?//= $image->id?><!--"-->
<!--                                        >X</span>-->
<!--                            </div>--><?php
//                        }
//                    }
//                    $j = 0;
//                    print $form->field($image, "[".$variant->id."][$j]id")->fileInput()->hint('Загрузить фото товара')->label('');
                    ?>
                </div>
                <!-- END >> add tags -->

                <?= $form->field($variant, "[".$variant->uniqueHashString."]status")->checkbox(['value' => 1,'label' => Yii::t('admin','Активность')]) ?>

            </div>
        </div>
        <hr />
        <?php
    }
    ?>
    <div class="blockForNewVariantsForms"></div>
    <div class="form-group variantFormAddThis">
        <span class="btn btn-primary addVariantFormForProvider"><?=Yii::t('admin','Добавить вариант')?></span>
    </div>

    <div class="form-group">
        <?= Html::submitButton($product->isNewRecord ? Yii::t('admin','Создать товар') : Yii::t('admin','Обновить товар'), ['class' => $product->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>


<?php
$form->end();

\app\models\Zloradnij::printArray($_POST);
