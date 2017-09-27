<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "deliveries_prices".
 *
 * @property integer $id
 * @property integer $delivery_id
 * @property integer $good_type_id
 * @property string $price
 * @property integer $status
 *
 * @property Deliveries $delivery
 * @property GoodsTypes $goodType
 */
class DeliveriesPrices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deliveries_prices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_id', 'good_type_id', 'status'], 'integer'],
            [['price'], 'number'],
            [['delivery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Deliveries::className(), 'targetAttribute' => ['delivery_id' => 'id']],
            [['good_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => GoodsTypes::className(), 'targetAttribute' => ['good_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'delivery_id' => 'Delivery ID',
            'good_type_id' => 'Good Type ID',
            'price' => 'Price',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDelivery()
    {
        return $this->hasOne(Deliveries::className(), ['id' => 'delivery_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodType()
    {
        return $this->hasOne(GoodsTypes::className(), ['id' => 'good_type_id']);
    }
}
