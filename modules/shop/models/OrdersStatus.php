<?php

namespace app\modules\shop\models;

use Yii;

/**
 * This is the model class for table "orders_status".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property integer $position
 * @property integer $status
 */
class OrdersStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'position', 'status'], 'integer'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }
}
