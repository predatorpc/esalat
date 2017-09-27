<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_moves".
 *
 * @property integer $id
 * @property integer $type
 * @property string $date
 * @property integer $store_from
 * @property integer $store_to
 * @property string $comments
 * @property integer $user_id
 * @property string $registration
 * @property integer $status
 */
class GoodsMoves extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_moves';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'store_from', 'store_to', 'user_id', 'status'], 'integer'],
            [['date', 'registration'], 'safe'],
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
            'type' => 'Type',
            'date' => 'Date',
            'store_from' => 'Store From',
            'store_to' => 'Store To',
            'comments' => 'Comments',
            'user_id' => 'User ID',
            'registration' => 'Registration',
            'status' => 'Status',
        ];
    }
}
