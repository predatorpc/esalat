<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "profile_links".
 *
 * @property integer $id
 * @property integer $profile_id
 * @property string $rate
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 */
class ProfileLinks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['profile_id'], 'required'],
            [['profile_id', 'status'], 'integer'],
            [['rate'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'profile_id' => 'Profile ID',
            'rate' => 'Rate',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }
}
