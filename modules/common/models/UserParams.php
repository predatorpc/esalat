<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "user_params".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property integer $value
 * @property integer $status
 */
class UserParams extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_params';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title'], 'required'],
            [['user_id', 'value', 'status'], 'integer'],
            [['title'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'value' => 'Value',
            'status' => 'Status',
        ];
    }
}
