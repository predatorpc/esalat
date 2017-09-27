<?php

namespace app\modules\coders\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property integer $id
 * @property string $time
 * @property integer $user_id
 * @property string $action
 * @property integer $shop_id
 * @property integer $store_id
 * @property integer $good_id
 * @property integer $variation_id
 * @property string $sql
 */
class Logs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['time','user_id', 'action', 'shop_id', 'store_id', 'good_id', 'variation_id', 'category_id', 'sql'], 'safe'],
            [['user_id', 'shop_id', 'store_id', 'good_id', 'variation_id'], 'integer'],
            [['sql'], 'string'],
            [['action'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time' => 'Time',
            'user_id' => 'User ID',
            'action' => 'Action',
            'shop_id' => 'Shop ID',
            'store_id' => 'Store ID',
            'good_id' => 'Good ID',
            'variation_id' => 'Variation ID',
            'sql' => 'Sql',
        ];
    }
}
