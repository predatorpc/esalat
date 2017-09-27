<?php

namespace app\modules\common\models;
use app\modules\questionnaire\models\Users;
use Yii;

/**
 * This is the model class for table "basket_fake_users".
 *
 * @property integer $id
 * @property integer $user
 * @property integer $fake_user
 * @property string $date
 * @property integer $status
 *
 * @property Users $user0
 * @property Users $fakeUser
 */
class BasketFakeUsers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'basket_fake_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user', 'fake_user', 'date'], 'required'],
            [['user', 'fake_user', 'status'], 'integer'],
            [['date'], 'safe'],
            [['user'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user' => 'id']],
            [['fake_user'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['fake_user' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user' => 'User',
            'fake_user' => 'Fake User',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(Users::className(), ['id' => 'user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFakeUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'fake_user']);
    }
}
