<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\GoodsTypes;
use app\modules\managment\models\ShopGroup;
use kartik\widgets\Select2;

$categoryList = ArrayHelper::map(Category::getFullCategoriesStructureList(),'id','title');

$typeList = ArrayHelper::map(GoodsTypes::find()->where(['status' => 1])->all(),'id','name');

$shopGroupList = ArrayHelper::map(ShopGroup::find()->where(['status' => 1])->all(),'id','name');
?>
<style>
    .not-visible{display:none;}
    .regular{cursor:pointer;pading-top:5px;padding-bottom: 5px;}
    .regular .passive{}
    .regular .active{color:#00aa00;text-shadow: 0 1px 0 #000;}
</style>
<div style="padding: 15px 0">
    <b><?=Yii::t('admin','Навигация')?></b><br>
    <?php foreach($menu['items'] as $key=>$item): ?>
        <a style="margin: 0 15px 0 0px;" href="<?=$item['link']?>"><?=Yii::t('admin',$item['title'])?></a>
    <?php endforeach; ?>
</div>
<div class="shop-form" id="cms-goods">
    <div class="product-form content-good">

        <?php $form = ActiveForm::begin([
            'options' => ['enctype'=>'multipart/form-data']
        ]); ?>

        <?= $form->field($model, "id")->textInput(['readonly' => true]); ?>
        <?= $form->field($model, "type_id")->DropDownList($typeList,['prompt' => Yii::t('admin','выберите тип')])->label(Yii::t('admin','Тип')); ?>
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
        <?= $form->field($category, "category_id")->DropDownList($categoryList,['prompt' => Yii::t('admin','выберите категорию')])->label(Yii::t('admin','Категория')); ?>

        <?= $form->field($shopGroup, "shop_group_id")->DropDownList($shopGroupList,['prompt' => Yii::t('admin','выберите группу магазинов')]);?>

        <?= $form->field($model, 'code')->textInput(['maxlength' => true])->label(Yii::t('admin','Артикул')); ?>
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
            <?= $form->field($model, 'iwish')->checkbox();?>
        </div>

        <label class="control-label" for="shops-id">Стикеры</label>
        <?= Select2::widget([
            'name' => 'sticker',
            'data' => ArrayHelper::map(\app\modules\catalog\models\Sticker::find()->where(['status'=>1])->all(),'id','name'),
            'value' => ArrayHelper::getColumn(\app\modules\catalog\models\StickerLinks::find()->where(['good_id'=>$model->id])->All(),'sticker_id'),
            'options' => [
                'placeholder' => 'Выбирите стикеры..',
                'multiple' => true
            ],
        ]);?>

        <?= $form->field($model, 'position')->textInput(['maxlength' => true])->label(Yii::t('admin','Сортировка')); ?>

        <?= $form->field($model, 'description')->widget(\dosamigos\tinymce\TinyMce::className(), [
            'options' => ['rows' => 6],
        ]) ?>
        <?= $form->field($model, 'seo_title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'seo_keywords')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'seo_description')->textInput(['maxlength' => true]) ?>

        <?php
        if($flag == 'update'){
            ?>
            <table class="table table-striped table-bordered">
                <tr>
                    <td colspan="2">
                        <div class="form-group">
                            <label class="control-label"><?=Yii::t('admin','Вариации товара')?></label>
                            <button
                                class="btn btn-primary add-product-variant-in-shops"
                                type="submit"
                                data-product="<?= $model->id ?>"
                                data-index="<?= !$variations?1:count($variations) ?>"
                            ><?=Yii::t('admin','Добавить вариацию')?></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <?php
                    if(isset($variations) && !empty($variations)){
                        foreach($variations as $i=>$variant) {
                            print \app\components\WCmsVariant::widget([
                                'variant' => $variant,
                                'shopsList' => $shopsList,
                                'shopStoreCount' => $shopStoreCount,
                                'tagGroup' => $tagGroup,
                                'tagValue' => $tagValue,
                                'variantProperties' => $variantProperties,
                                'i' => $i,
                                'form' => $form,
                                'images' => $images,
                            ]);
                        }
                    }?>
                    </td>
                </tr>
            </table>
            <?php
        }
        ?>

        <?php
        echo Html::submitButton(Yii::t('admin','Сохранить'), ['class' => 'btn btn-primary']);


        ActiveForm::end();
    //    \app\models\Zloradnij::printArray($_POST);
        ?>
    </div>

</div>