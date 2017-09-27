<?php

namespace app\modules\managment\models;

use Yii;

/**
 * This is the model class for table "shops_stores_times".
 *
 * @property integer $id
 * @property integer $store_id
 * @property integer $day
 * @property string $time_begin
 * @property string $time_end
 * @property integer $status
 */
class ShopsStoresTimes extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shops_stores_times';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'day', 'status'], 'integer'],
            [['time_end', 'time_begin'], 'match', 'pattern' => '/^(([0,1][0-9])|(2[0-3])):[0-5][0-9]:[0-5][0-9]$/i', 'message' => 'Введите время корректно ЧЧ:ММ:СС, например 20:15:00'],
//        [['time_begin', 'time_end'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'day' => 'Day',
            'time_begin' => 'Time Begin',
            'time_end' => 'Time End',
            'status' => 'Status',
        ];
    }
}
