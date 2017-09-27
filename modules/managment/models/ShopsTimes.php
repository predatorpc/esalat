<?php

namespace app\modules\managment\models;

use Yii;

/**
 * This is the model class for table "shops_times".
 *
 * @property integer $id
 * @property integer $shop_id
 * @property integer $day
 * @property string $time
 * @property integer $delay
 * @property integer $status
 */
class ShopsTimes extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shops_times';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'day', 'delay', 'status'], 'integer'],
            [['time'], 'safe']
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
            'day' => 'Day',
            'time' => 'Time',
            'delay' => 'Delay',
            'status' => 'Status',
        ];
    }
}
