<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_types".
 *
 * @property integer $id
 * @property string $name
 * @property integer $time
 * @property integer $delivery_id
 * @property integer $position
 * @property integer $status
 */
class GoodsTypes extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time', 'delivery_id', 'position', 'status'], 'integer'],
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
            'time' => 'Time',
            'delivery_id' => 'Delivery ID',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }
}
