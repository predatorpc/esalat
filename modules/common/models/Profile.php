<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "profile".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $age
 * @property integer $gender
 * @property integer $pets
 * @property integer $children
 * @property integer $car
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 */
class Profile extends \yii\db\ActiveRecord
{
    public $user_name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'age', 'gender', 'pets', 'children', 'car', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getProfileLinks()
    {
        /*return ProfileLinks::find()
            ->leftJoin('profile', 'profile.id = profile_links.profile_id')
            ->where('profile_links.status = 1')
            ->orderBy('profile_links.created_at DESC')
            ->limit(1)
            ->all();*/
        return $this->hasOne(ProfileLinks::className(), ['profile_id' => 'id'])->orderBy(['profile_links.created_at' => SORT_DESC]);
    }

    public function getUsers()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUsersName()
    {
        return $this->getUsers()->name;
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'age' => 'Age',
            'gender' => 'Gender',
            'pets' => 'Pets',
            'children' => 'Children',
            'car' => 'Car',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }
}
