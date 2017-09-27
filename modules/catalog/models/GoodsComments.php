<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_comments".
 *
 * @property integer $id
 * @property integer $good_id
 * @property integer $user_id
 * @property string $name
 * @property string $text
 * @property string $date
 * @property integer $status
 */
class GoodsComments extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['good_id', 'user_id', 'status'], 'integer'],
            [['text'], 'required'],
            [['text'], 'string'],
            [['date'], 'safe'],
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
            'good_id' => 'Good ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'text' => 'Text',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }
}
