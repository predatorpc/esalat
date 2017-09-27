<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_edits".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $good_id
 * @property integer $user_id
 * @property string $date
 * @property integer $status
 */
class GoodsEdits extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_edits';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'good_id', 'user_id', 'status'], 'integer'],
            [['date'], 'safe']
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
            'good_id' => 'Good ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }
}
