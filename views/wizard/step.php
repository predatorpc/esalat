<?php
use yii\widgets\ListView;
use app\components\WProductItemOneMini;

$this->title = !empty($model->seo_title) ? $model->seo_title : 'Новинки';

$url = '/' . Yii::$app->params['catalogPath'] . '/';
$breadcrumbsUrl = '';
$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => $url];

$this->params['breadcrumbs'][] = $this->title;

//print_arr(print_arr(Yii::$app->user));
?>

<div class="content">
    <!--Хлебная крошка-->
    <!-- div class="path">
        <a href="/">Главная</a>
    </div -->    <!--Хлебная крошка-->
    <div class="row">
        <div class="goods">
            <?= ListView::widget([
                'dataProvider' => $dataProviderProducts,
                'options' => [
                    'tag' => 'div',
                    'class' => 'product-list js-product-list mod___goods_list  goods-top',
                    'id' => 'list-wrapper',
                ],
                'layout' => "<div class='items'>{items}<div class='clear'></div></div>",
                'itemView' => function ($model){
                    return WProductItemOneMini::widget([
                        'model' => $model,
                        'user' => Yii::$app->user->can('categoryManager'),
                    ]);
                },

            ]); ?>
        </div>
    </div>
    <div class="clear"></div>
</div>