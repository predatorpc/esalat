<?php

namespace app\modules\shop\models;

use app\modules\common\models\ActiveRecordRelation;
use Yii;

/**
 * This is the model class for table "orders_selects".
 *
 * @property integer $id
 * @property integer $order_group_id
 * @property integer $user_id
 * @property string $price
 * @property string $date_begin
 * @property string $date_end
 * @property string $comments
 * @property integer $status
 */
class OrdersSelects extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_selects';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_group_id', 'user_id', 'status'], 'integer'],
            [['price'], 'number'],
            [['date_begin', 'date_end'], 'safe'],
            [['comments'], 'required'],
            [['comments'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_group_id' => 'Order Group ID',
            'user_id' => 'User ID',
            'price' => 'Price',
            'date_begin' => 'Date Begin',
            'date_end' => 'Date End',
            'comments' => 'Comments',
            'status' => 'Status',
        ];
    }
}
