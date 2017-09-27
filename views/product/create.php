<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\GoodsVariations */

$this->title = Yii::t('admin', 'Новый продукт');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Продукты'), 'url' => ['product-list']];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Добавить');
?>
<div class="product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_product', [
        'model' => $model,
        'category' => $category,
        'variations' => $variations,
        'shopStoreCount' => $shopStoreCount,
        'variantProperties' => $variantProperties,
        'tagGroup' => $tagGroup,
        'tagValue' => $tagValue,
        'shopsList' => $shopsList,
        'shopGroup' => $shopGroup,
        'images' => $images,
        'flag' => 'create',
        'menu' =>  $menu,
    ]) ?>

</div>
