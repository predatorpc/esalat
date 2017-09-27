<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "deliveries".
 *
 * @property integer $id
 * @property integer $shop_id
 * @property string $name
 * @property integer $address
 * @property integer $position
 * @property integer $status
 */
class Deliveries extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deliveries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'address', 'position', 'status'], 'integer'],
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
            'shop_id' => 'Shop ID',
            'name' => 'Name',
            'address' => 'Address',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }
}
