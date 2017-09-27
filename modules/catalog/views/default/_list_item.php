<?php
// _list_item.php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<article class="item" data-key="<?= $model->id; ?>">
    <h2 class="title">
        <?= Html::a(
            Html::encode($model->title),
            Url::toRoute(['catalog/'.$model->alias]), ['title' => $model->title]
            //$this->createAbsoluteUrl('catalog/alias', ['alias'=>$model->alias]),
        ) ?>
    </h2>
</article>