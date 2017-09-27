<?php
use yii\widgets\ListView;
use app\components\WProductItemOne;

$this->title = !empty($model->seo_title) ? $model->seo_title : Yii::t('app','Новинки');

$url = '/' . Yii::$app->params['catalogPath'] . '/';
$breadcrumbsUrl = '';
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('app','Каталог'), 'url' => $url];

$this->params['breadcrumbs'][] = $this->title;

//print_arr(print_arr(Yii::$app->user));
?>
<div class="content">
    <div class="row">
        <h1 class="title"><?=$this->title?></h1>
        <div class="goods goods-new">
            <?= ListView::widget([
                'dataProvider' => $dataProviderProducts,
                'options' => [
                    'tag' => 'div',
                    'class' => 'product-list js-product-list mod___goods_list  goods-top',
                    'id' => 'list-wrapper',
                ],
                'layout' => "<div class='items'>{items}<div class='clear'></div></div>",
                'itemView' => function ($model){
//                print_r($model->name);echo "</br>";
                    return WProductItemOne::widget([
                        'model' => $model,
                        'user' => Yii::$app->user->can('categoryManager'),
                    ]);
                },

            ]); ?>
        </div>
    </div>
    <div class="clear"></div>
</div>