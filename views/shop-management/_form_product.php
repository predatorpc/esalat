<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\GoodsTypes;
use app\modules\managment\models\ShopGroup;
use dosamigos\tinymce\TinyMce;

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
<div class="product-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype'=>'multipart/form-data']
    ]); ?>

    <?= $form->field($model, "type_id")->DropDownList($typeList,['prompt' => 'выберите тип'])->label('Тип'); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
    <?= $form->field($category, "category_id")->DropDownList($categoryList,['prompt' => 'выберите категорию'])->label('Категория'); ?>

    <?= $form->field($shopGroup, "shop_group_id")->DropDownList($shopGroupList,['prompt' => 'выберите группу магазинов']);?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true])->label('Артикул'); ?>
    <?= $form->field($model, 'count_pack')->textInput(['maxlength' => true,'id' => 'count_pack']); ?>
    <?= $form->field($model, 'count_min')->textInput(['maxlength' => true]); ?>

    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 checkbox__shop">
        <?= $form->field($model, 'show')->checkbox();?>
        <?= $form->field($model, 'confirm')->checkbox();?>
        <?= $form->field($model, 'autolink')->checkbox();?>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 checkbox__shop">
        <?= $form->field($model, 'status')->checkbox();?>
        <?= $form->field($model, 'bonus')->checkbox();?>
        <?= $form->field($model, 'discount')->checkbox();?>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 checkbox__shop">
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

    <?php
    if($flag == 'update'){
        ?>
        <table class="table table-striped table-bordered">
            <tr>
                <td colspan="2">
                    <div class="form-group">
                        <label class="control-label">Вариации товара</label>
                        <button
                            class="btn btn-primary add-product-variant-in-shops"
                            type="submit"
                            data-product="<?= $model->id ?>"
                            data-index="<?= !$variations?1:count($variations) ?>"
                        >Добавить вариацию</button>
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
    echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    ActiveForm::end();
//    \app\models\Zloradnij::printArray($_POST);
    ?>
</div>