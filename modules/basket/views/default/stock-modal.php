<?php

use yii\helpers\Html;
use app\modules\common\models\ModFunctions;
use app\modules\basket\models\BasketProducts;
use app\modules\catalog\models\Goods;


$this->title = Yii::t('app','Ваш товар добавлен');
$this->params['breadcrumbs'][] = $this->title;

$basket_id = \Yii::$app->basket->basket->getPresentInBasket();
if(!empty($basket_id)) {
$basketProducts = BasketProducts::find()->where(['id'=>$basket_id,'status'=>1])->one();
?>
    <div style="border: 1px solid #0003; padding: 5px;border-radius: 3px;" >
        <div class="images" style="float: left;padding: 0 5px 0 0px;width: 100px;overflow: hidden;height: 90px;"><img src="http://www.esalad.ru<?=Goods::findProductImage($basketProducts->product->id,'min')?>?>" width="100%"></div>
        <p>
            В Вашу корзину был добавлен Акционный товар <b><?=$basketProducts->variant->full_name?></b> со скидкой по супер цене! Для изменения списка товаров, перейдите в <a href="/basket/">Корзину</a>.
        </p>
    </div>
    <div class="clear"></div>
<?php }?>
