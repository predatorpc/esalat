<?php

namespace app\modules\wishlist\models;

use Yii;
use app\modules\catalog\models\Goods;

/**
 * This is the model class for table "wishlist_products".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $product_id
 * @property integer $status
 */
class WishlistProducts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wishlist_products';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'product_id'], 'required'],
            [['user_id', 'product_id', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'product_id' => 'Product ID',
            'status' => 'Status',
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Goods::className(), ['id' => 'product_id']);
    }
}
