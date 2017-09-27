<?php

namespace app\modules\questionnaire\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property integer $city_id
 * @property integer $extremefitness
 * @property string $name
 * @property string $birthday
 * @property integer $s
 * @property string $phone
 * @property string $secret_word
 * @property string $email
 * @property integer $updated_at
 * @property integer $created_at
 * @property string $password_reset_token
 * @property string $password_hash
 * @property string $auth_key
 * @property string $password
 * @property string $money
 * @property string $bonus
 * @property string $hash
 * @property integer $staff
 * @property integer $driver
 * @property integer $manager
 * @property integer $level
 * @property integer $call
 * @property integer $store_id
 * @property integer $sms
 * @property string $enter
 * @property string $registration
 * @property integer $confirm
 * @property integer $agree
 * @property integer $typeof
 * @property integer $compliment
 * @property integer $status
 * @property string $invite_promo
 * @property integer $autoreg
 * @property integer $action_id
 *
 * @property Address[] $addresses
 * @property Codes[] $codes
 * @property FavoriteGroup[] $favoriteGroups
 * @property QuestionnaireAnswers[] $questionnaireAnswers
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id', 'extremefitness', 's', 'updated_at', 'created_at', 'staff', 'driver', 'manager', 'level', 'call', 'store_id', 'sms', 'confirm', 'agree', 'typeof', 'compliment', 'status', 'autoreg', 'action_id'], 'integer'],
            [['birthday', 'enter', 'registration'], 'safe'],
            [['money', 'bonus'], 'number'],
            [['name', 'email'], 'string', 'max' => 64],
            [['phone'], 'string', 'max' => 16],
            [['secret_word', 'password_reset_token', 'password_hash'], 'string', 'max' => 255],
            [['auth_key', 'password', 'hash'], 'string', 'max' => 32],
            [['invite_promo'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => 'City ID',
            'extremefitness' => 'Extremefitness',
            'name' => 'Name',
            'birthday' => 'Birthday',
            's' => 'S',
            'phone' => 'Phone',
            'secret_word' => 'Secret Word',
            'email' => 'Email',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'password_reset_token' => 'Password Reset Token',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'password' => 'Password',
            'money' => 'Money',
            'bonus' => 'Bonus',
            'hash' => 'Hash',
            'staff' => 'Staff',
            'driver' => 'Driver',
            'manager' => 'Manager',
            'level' => 'Level',
            'call' => 'Call',
            'store_id' => 'Store ID',
            'sms' => 'Sms',
            'enter' => 'Enter',
            'registration' => 'Registration',
            'confirm' => 'Confirm',
            'agree' => 'Agree',
            'typeof' => 'Typeof',
            'compliment' => 'Compliment',
            'status' => 'Status',
            'invite_promo' => 'Invite Promo',
            'autoreg' => 'Autoreg',
            'action_id' => 'Action ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(Address::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodes()
    {
        return $this->hasMany(Codes::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavoriteGroups()
    {
        return $this->hasMany(FavoriteGroup::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionnaireAnswers()
    {
        return $this->hasMany(QuestionnaireAnswers::className(), ['user_id' => 'id']);
    }
}
