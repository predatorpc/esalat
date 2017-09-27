<?php

//use Yii;
use yii\data\ActiveDataProvider;
use app\models\BasketProducts;
use app\models\Goods;
use app\models\Category;
use app\models\BasketOne;

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;

$category = new Category();
?>
    <div class="container text basket-index col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="title"><h1><?=$this->title?></h1></div>
        <div class="basket-goods col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php
            if(isset(\Yii::$app->controller->basket['productsShort']) && !empty(\Yii::$app->controller->basket['productsShort'])){
                ?>
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th class="image">Фото</th>
                        <th class="name">Название</th>
                        <th class="count">Количество</th>
                        <th class="price">Цена</th>
                        <th class="money">Сумма</th>
                        <th class="delete"></th>
                    </tr>
                    <?php
                    foreach(\Yii::$app->controller->basket['productTypeList'] as $keyType => $productsOfTypes){
                        print \app\components\WBasketTypeProduct::widget(['typeName' => $typeProducts[$keyType]]);

                        $productsOfTypes = array_unique($productsOfTypes);
                        foreach($productsOfTypes as $productId){
                            $dataProviderProducts  = $category->findProduct(Yii::$app->controller->basket['products'][$productId]['productId']);
                            $product = $dataProviderProducts->getModels();

                            print \app\components\WBasketProduct::widget([
                                'product' => $productId,
                                'catalogItem' => $product[0],
                                'variationsAllProductsList' => !empty(Yii::$app->controller->basket['products'][$productId]['productId']) ? $category->findVariations([Yii::$app->controller->basket['products'][$productId]['productId']]) : [],
                            ]);
                        }
                    }
                    ?>
                </table>

                <?= \app\components\WDeliverySelect::widget(['delivery' => $delivery,'sort' => 2]);?>

                <?php
                $timeList = [];
                foreach(\Yii::$app->controller->basket['productTypeForJob'][\Yii::$app->controller->basket['deliveryId']] as $productsTypesList){
                    if(!empty($productsTypesList)){
                        reset($productsTypesList);
                        $timeList[] = BasketOne::getDeliveryTime(current($productsTypesList),\Yii::$app->controller->basket['deliveryId']);
                    }
                }

                print \app\components\WBasketTimeDelivery::widget([
                    'time' => $timeList,
                    'sort' => 3
                ]);
                ?>

                <?= \app\components\WPaymentSelect::widget()?>
                <?= \app\components\WBasketComment::widget(['comment' => \Yii::$app->controller->basket['comment']])?>
                <?php
                //$_SESSION['basket-yii']['promoCode'] = '';
                ?>

                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
                    <?= \app\components\WBasketPromo::widget(['promo' => \Yii::$app->session['shop']['basket']['promoCode'],'message' => ''])?>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
                    <?= \app\components\WBasketResult::widget()?>
                </div>

                <?php
            }else{
                ?>
                <div>Ничего не выбрали? В нашем каталоге огромный выбор товаров, посмотрите <a href="/catalog/new/">еще</a></div>
                <?php
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>
<?php
/*
print '<pre>$basketProductsTypes';
print_r($basketProductsTypes);
print '</pre>';
print '<pre>$basketProducts';
print_r($basketProducts);
print '</pre>';
print '<pre>$basketProductsVariant';
print_r($basketProductsVariant);
print '</pre>';
print '<pre>$basketVariants';
print_r($basketVariants);
print '</pre>';
print '<pre>$basketProductsVariantTags';
print_r($basketProductsVariantTags);
print '</pre>';


print '<pre>';
                print_r($basket);
                print '</pre>';
<pre>
<?php print_r(\Yii::$app->session)?>
</pre>

<pre>
<?php print_r(\Yii::$app->session['basket'])?>
</pre>

*/
//md5('9137172874');
/*
print substr(md5('+79137172874'),1,8);
print substr(md5('+79137929000'),1,8);
print '<pre>$basketTest ';
//print_r($basketTest);
print '</pre>';
print '<pre>$basket ';
//print_r(\Yii::$app->session['basket']['goods']);
print '</pre>';
*/
print '<pre>$basket ';
//print_r($_SESSION);
print '</pre>';



