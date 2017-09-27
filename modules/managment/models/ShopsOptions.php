<?php

namespace app\modules\managment\models;

use Yii;

/**
 * This is the model class for table "shops_options".
 *
 * @property integer $id
 * @property integer $shop_id
 * @property integer $created_user
 * @property string $date_start
 * @property string $date_end
 * @property string $date
 * @property string $money
 * @property integer $status
 */
class ShopsOptions extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shops_options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'created_user'], 'required'],
            [['shop_id', 'created_user', 'status'], 'integer'],
            [['date_start', 'date_end', 'date'], 'safe'],
            [['money'], 'number']
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
            'created_user' => 'Created User',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
            'date' => 'Date',
            'money' => 'Money',
            'status' => 'Status',
        ];
    }
}
