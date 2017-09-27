<?php
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\Category;
use yii\helpers\Url;
use yii\web\CatalogAsset;

$this->registerCssFile('@web/css/catalog.css');
$this->title = Yii::t('admin', 'Поиск');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if(empty($error)): ?>
    <div class="content">
        <?php if(!empty($result)): ?>
            <div class="title"><h1>Поиск: <?=$search?></h1></div>
        <?php endif; ?>
        <div id="search" class="goods">
            <?php if(!empty($result)): ?>
                <?php if(!empty($result['category'])): ?>

                    <!--Результат поиска категория-->
                    <?php foreach($result['category'] as $key=>$value):

                        if(!empty($value['parent_title'])){

                        ?>
                        <div class="item col-md-6 col-sm-6">
                            <a class="goods" href="/search?category=<?=$value['id'] ?>&search=<?=$search?>">
                                <img src="http://www.esalad.ru/<?= Goods::findProductImage($value['image_id']);?>" alt="" class="images col-xs-3">
                                <div class="title col-xs-9"><?=$value['parent_title'].' / '.$value['title'];?></div>
                                <div class="clear"></div>
                            </a>
                        </div>
                    <?php } endforeach; ?>

                    <div class="clear"></div>
                <?php endif; ?>
                <!--Результат поиска товара-->
                <?php if(!empty($result['goods'])): ?>
                    <h2 class="h1_goods">Результаты поиска</h2>

        </div>
        <div id="search_off" class="goods">

                    <?php


                    if(!empty($result['goods'])){
                        $ids = [];
                        foreach ($result['goods'] as $good) {
                            $ids[] = $good;
                        }

                        print \yii\widgets\ListView::widget([
                            'dataProvider' => new \yii\data\ActiveDataProvider(['query' => Goods::find()->where(['IN','id',$ids])]),
                            'options' => [
                                'tag' => 'div',
                                'class' => 'product-list js-product-list mod___goods_list goods-list goods-top',
                                'id' => 'list-wrapper',
                            ],
                            'layout' => "<div class='items '>{items}<div class='clear'></div></div>\n{pager}",
                            'itemView' => function ($model){
                                return \app\components\WProductItemOne::widget([
                                    'model' => $model,
                                    'user' => Yii::$app->user->can('categoryManager'),
                                ]);
                            },

                        ]);
                    }
                    ?>

                    <?php   /* foreach($result['goods'] as $key=>$value): ?>
                        <div class="item"><a class="goods"  href="<?= Goods::getPath($value['id'])?>"><img src="http://www.extremeshop.ru/<?= Goods::findProductImage($value['image_id']);?>" alt="" class="ad images"><?=$value['name'] ?></a></div>
                    <?php endforeach;  */?>
                <?php endif; ?>
                <br>
                <br>

                <!--Возможно вы искали-->
                <?php if(!empty($result['maybe'])): ?>
                    <h2 class="h1_goods">Возможно вы искали</h2>
                    <?php foreach($result['maybe'] as $key=>$value):
                        if(!empty($value['parent_alias']))
                            {
                                ?>
                          <div class="item col-md-6 col-sm-6">
                            <?php

                            $category = Category::findOne($value['id']);
                            if(!empty($category)){?>
                                <a class="goods"  href="/<?=$category->catalogPath?>"><?php
                            }else{?>
                                <a class="goods"  href="/catalog/new/"><?php
                            }
                            ?>

                                <img src="http://www.esalad.ru/<?= Goods::findProductImage($value['image_id']);?>" alt="" class="images col-xs-3">
                                    <div class="title col-xs-9"><?=$value['title'];?><?php //$value['parent_title'];?></div>
                                    <div class="clear"></div>
                            </a>

                        </div>
                    <?php
                            }
                        endforeach;
                    ?>
                    <div class="clear"></div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>

<?php  else: ?>
    <div class="title"><h1>Поиск: </h1></div>
    <br>
    <br>
    <p><?= $error ?></p>

<?php endif; ?>