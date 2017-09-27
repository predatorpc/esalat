<?php

namespace app\modules\managment\models;

use Yii;

/**
 * This is the model class for table "shops_deliveries".
 *
 * @property integer $id
 * @property integer $shop_id
 * @property integer $delivery_id
 * @property integer $status
 */
class ShopsDeliveries extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shops_deliveries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'delivery_id', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => 'Shop ID',
            'delivery_id' => 'Delivery ID',
            'status' => 'Status',
        ];
    }
}
