<?php

namespace app\modules\basket\models;

use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\Lists;
use Yii;
use yii\db\Query;

class BasketShop extends Basket
{
    public $priceDiscount;
    
    private function mergerBasket($basketSession = false, $basketBase = false){
        if(!$basketBase && !$basketSession){
            $basket = new BasketShop();

            $basket->address_id = 0;
            $basket->delivery_id = 0;
            $basket->payment_id = 0;
            $basket->user_id = Yii::$app->user->isGuest ? NULL : Yii::$app->user->identity->id;
            $basket->session_id = $this->getBasketSessionId();
            $basket->status = 0;

            $basket->save();

            return $basket;
        }

        if(!$basketSession){
            $basketBase->session_id = $this->getBasketSessionId();
            $basketBase->save();
            return $basketBase;
        }

        if(!$basketBase){
            $basketSession->session_id = $this->getBasketSessionId();
            $basketSession->user_id = Yii::$app->user->isGuest ? false : Yii::$app->user->identity->id;
            $basketSession->save();

            return $basketSession;
        }

        if($basketBase->id == $basketSession->id){
            $basketBase->session_id = $this->getBasketSessionId();
            $basketBase->save();
            return $basketBase;
        }

        if(count($basketSession->products) > 0){
            $basketBase->delete();
            $basketSession->user_id = Yii::$app->user->isGuest ? false : Yii::$app->user->identity->id;
            $basketSession->save();
            return $basketSession;
        }

        $basketBase->session_id = $this->getBasketSessionId();
        $basketBase->save();
        $basketSession->delete();
        return $basketBase;
    }

    private function getBasketSessionId(){
        return !empty(Yii::$app->session['basket-session-id']) ? Yii::$app->session['basket-session-id'] : Yii::$app->session->id;
    }

    public function findCurrentBasket(){
        $basketBase = !empty(Yii::$app->user->identity) ? self::find()->where(['status' => 0,'user_id' => Yii::$app->user->identity->id])->one() : false;
        $basketSession = self::find()->where(['status' => 0,'session_id' => $this->getBasketSessionId()])->one();

        return $this->mergerBasket($basketSession,$basketBase);
    }

    public function getDeliveryAddresses(){
        $userAddress = [];

        if(!empty($this->user_id)){
            $userAddress = (new Query())->from('address')
                ->select([
                    '\'1006\' AS `id`',
                    'id AS value',
                    'delivery_id',
                    'CONCAT_WS(\', \', district, street, house, room) AS address',
                ])
                ->where(['user_id' => $this->user_id])
                ->andWhere(['status' => 1])
                ->all();
        }

        return $userAddress;
    }

    public function getBasketList(){
        $lists = [];
        if(!empty($this->products)){
            foreach ($this->products as $i => $product) {
                if(!empty($product->list_id)){
                    $lists[] = $product->list_id;
                }
            }
        }
        if(!empty($lists)){
            $lists = array_unique($lists);
            return Lists::find()->where(['IN','id',$lists])->all();
        }
        return [];
    }

    public function getBasketProductsSimple(){
        return $this->hasMany(BasketProducts::className(), ['basket_id' => 'id'])->where(['list_id',0]);
    }

    public function getBasketProductsList(){
        return $this->hasMany(BasketProducts::className(), ['basket_id' => 'id'])->where(['>','list_id',0]);
    }

    public function add($variantId,$count = 1){
        $variant = GoodsVariations::findOne($variantId);
        $basketProduct = new BasketProducts();
        $basketProduct->basket_id = $this->id;
        $basketProduct->product_id = $variant->good_id;
        $basketProduct->variant_id = $variant->id;
        $basketProduct->count = $count;
        $basketProduct->store_id = $variant->product->defaultStore;

        $basketProduct->list_id = 0;
        $basketProduct->tool = Yii::$app->session->get('shopMaster',0) > 0 ? 1 : 0;

        parent::addProduct($basketProduct);
    }
}
