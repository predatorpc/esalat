<?php

namespace app\modules\basket\models;

use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsVariations;
use app\modules\managment\models\ShopsStores;

/**
 * This is the model class for table "basket_products".
 *
 * @property integer $id
 * @property integer $basket_id
 * @property integer $product_id
 * @property integer $variant_id
 * @property integer $count
 * @property integer $store_id
 * @property integer $list_id
 * @property integer $tool
 */
class BasketProducts extends \yii\db\ActiveRecord
{
    public $price = 0;
    public $priceDiscount = 0;
    public $bonus = 0;
    public $fee = 0;
    public $commission = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'basket_products';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['basket_id', 'product_id', 'variant_id', 'count'], 'required'],
            [['basket_id', 'product_id', 'variant_id', 'count','store_id','list_id','tool', 'bonusBack', 'rublBack', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'basket_id' => 'Basket ID',
            'product_id' => 'Product ID',
            'variant_id' => 'Variant ID',
            'count' => 'Count',
            'store_id' => 'Store_id',
            'list_id' => 'List ID',
            'tool' => 'Tool ID',
            'bonusBack' => 'Bonus Back',
            'rublBack' => 'Rubl Back',
            'status' => 'Status',
        ];
    }

    public function checkPay(){

        return (!empty($this->variant) && $this->variant->status == 1) ? true : false;
    }

    public function getVariant(){
        return $this->hasOne(GoodsVariations::className(),['id' => 'variant_id']);
    }

    public function getProduct(){
        return $this->hasOne(Goods::className(),['id' => 'product_id']);
    }
    public function getProductAll(){
        return $this->hasMany(Goods::className(),['id' => 'product_id']);
    }
    public function getBasket(){
        return $this->hasOne(Basket::className(),['basket_id' => 'id']);
    }

    public function getStore(){
        return $this->hasOne(ShopsStores::className(),['address_id' => 'store_id']);
    }

    public function getPropertyList(){
        $variationList = [];
        foreach ($this->product->variationsCatalog as $variant) {
            foreach ($variant->propertiesFrontVisible as $item) {
                $variationList[$variant->id][$item->group_id][$item->id] = $item->value;
            }
        }
        return $variationList;
    }
}
