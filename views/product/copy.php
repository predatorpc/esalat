<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Shops */

$this->title = Yii::t('admin', 'Копия продукта');
$this->params['breadcrumbs'][] = ['label' => 'Продукты', 'url' => ['/shop/goods']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shops-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_product_v2', [
        'model' => $model,
        'typeProduct' => $typeProduct,
        'producers' => $producers,
        'country' => $country,
        'modelVariant' => $modelVariant,
        'tagsList' => $tagsList,
        'tagsListValue' => $tagsListValue,
        'tags' => $tags,
        'variantImages' => $variantImages,
        'countVariation' => $countVariation,
        'category' => $category,
        'shopsList' => $shopsList,
        'shopGroup' => $shopGroup,
        'shopStoreCount' => $shopStoreCount,
        'images' => $images,
        'menu' =>  $menu,
    ]) ?>

</div>
