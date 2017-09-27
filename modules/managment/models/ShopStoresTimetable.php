<?php

namespace app\modules\managment\models;

use Yii;

/**
 * This is the model class for table "shop_stores_timetable".
 *
 * @property integer $id
 * @property integer $store_id
 * @property integer $day
 * @property string $time_begin
 * @property string $time_end
 * @property integer $status
 *
 * @property ShopsStores $store
 */
class ShopStoresTimetable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_stores_timetable';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'day','time_begin', 'time_end'],'required'],
            [['store_id', 'day', 'status'], 'integer'],
            [['time_begin', 'time_end'], 'string', 'max' => 100],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShopsStores::className(), 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Склад',
            'day' => 'День',
            'time_begin' => 'Начало работы',
            'time_end' => 'Конец работы',
            'status' => 'Статус',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(ShopsStores::className(), ['id' => 'store_id']);
    }
}
