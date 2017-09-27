<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "users_address".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $district_id
 * @property string $street
 * @property string $house
 * @property string $room
 * @property string $comments
 * @property string $phone
 * @property string $date
 * @property integer $status
 */
class UsersAddress extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'district_id', 'status'], 'integer'],
            [['comments'], 'required'],
            [['comments'], 'string'],
            [['date'], 'safe'],
            [['street'], 'string', 'max' => 64],
            [['house', 'phone'], 'string', 'max' => 16],
            [['room'], 'string', 'max' => 8]
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
            'district_id' => 'District ID',
            'street' => 'Street',
            'house' => 'House',
            'room' => 'Room',
            'comments' => 'Comments',
            'phone' => 'Phone',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }
}
