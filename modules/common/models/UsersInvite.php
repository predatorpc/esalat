<?php

namespace app\modules\common\models;

use Yii;
use app\modules\catalog\models\Codes;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property integer $city_id
 * @property integer $extremefitness
 * @property string $name
 * @property integer $invite_promo
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
 * @property integer $status
 *
 * @property Address[] $addresses
 * @property Codes[] $codes
 * @property FavoriteGroup[] $favoriteGroups
 * @property Goods[] $goods
 * @property GoodsBackup[] $goodsBackups
 * @property GoodsComments[] $goodsComments
 * @property GoodsEdits[] $goodsEdits
 * @property GoodsMoves[] $goodsMoves
 * @property LinksBanners[] $linksBanners
 * @property Lists[] $lists
 * @property Messages[] $messages
 * @property NotificationsUsers[] $notificationsUsers
 * @property Orders[] $orders
 * @property OrdersItemsComments[] $ordersItemsComments
 * @property OrdersItemsStatus[] $ordersItemsStatuses
 * @property OrdersNotices[] $ordersNotices
 * @property OrdersSelects[] $ordersSelects
 * @property Shops[] $shops
 * @property UsersAddress[] $usersAddresses
 * @property UsersBonus[] $usersBonuses
 * @property UsersCards[] $usersCards
 * @property UsersDrivers[] $usersDrivers
 * @property UsersPays[] $usersPays
 * @property UsersPins[] $usersPins
 * @property UsersRoles[] $usersRoles
 * @property UsersSocials[] $usersSocials
 */
class UsersInvite extends \yii\db\ActiveRecord
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
            [['name', 'email','phone','password_hash','invite_promo'],'required','message' => 'Обязательное поле'],
            [['created_at', 'status','agree','autoreg'], 'integer'],
            //['name', 'string', 'max' => 64],
            ['name','match', 'pattern'=> '/^[А-я\s]+$/u','message'=>'Используйте только кириллицу'],
            ['phone','validatePhone'],
            ['email','email'],
            //['invite_promo','exist', 'skipOnError'=>true, 'targetClass'=>Codes::className(), 'targetAttribute'=>['invite_promo', 'id']],
            ['invite_promo','validatePromo'],
            ['agree','validateAgree'],
            ['autoreg','validateAutoreg']
        ];
    }

    public function validatePromo()
    {
        $invite_promo = Codes::find()->where(['code' => $this->invite_promo])->One();
        if(!$invite_promo || empty($this->invite_promo)){
            $this->addError('invite_promo', 'Не существующий промокод');
        }

    }

    public function validatePhone()
    {
        $phone = '+7'.str_replace('-','',$this->phone);
        $user = UsersInvite::find()->where(['phone' => $phone])->One();
        if($user){
            $this->addError('phone', 'Данный номер уже используется');
        }

    }

    public function validateAgree()
    {
        if($this->agree == 0 ){
            $this->addError('agree', 'Прочтите соглашение');
        }

    }

    public function validateAutoreg()
    {
        if($this->autoreg == 0 ){
            $this->addError('autoreg', 'Текст ошибки!');
        }

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
            'invite_promo' => 'Invite Promo',
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
            'agree' => 'Я ознакомлен и согласен с пользовательским соглашением.',
            'typeof' => 'Typeof',
            'status' => 'Status',
            'autoreg' => 'Авторегистрация'
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
    public function getGoods()
    {
        return $this->hasMany(Goods::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsBackups()
    {
        return $this->hasMany(GoodsBackup::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsComments()
    {
        return $this->hasMany(GoodsComments::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsEdits()
    {
        return $this->hasMany(GoodsEdits::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsMoves()
    {
        return $this->hasMany(GoodsMoves::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinksBanners()
    {
        return $this->hasMany(LinksBanners::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLists()
    {
        return $this->hasMany(Lists::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Messages::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationsUsers()
    {
        return $this->hasMany(NotificationsUsers::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersItemsComments()
    {
        return $this->hasMany(OrdersItemsComments::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersItemsStatuses()
    {
        return $this->hasMany(OrdersItemsStatus::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersNotices()
    {
        return $this->hasMany(OrdersNotices::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersSelects()
    {
        return $this->hasMany(OrdersSelects::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShops()
    {
        return $this->hasMany(Shops::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersAddresses()
    {
        return $this->hasMany(UsersAddress::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersBonuses()
    {
        return $this->hasMany(UsersBonus::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersCards()
    {
        return $this->hasMany(UsersCards::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersDrivers()
    {
        return $this->hasMany(UsersDrivers::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersPays()
    {
        return $this->hasMany(UsersPays::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersPins()
    {
        return $this->hasMany(UsersPins::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersRoles()
    {
        return $this->hasMany(UsersRoles::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersSocials()
    {
        return $this->hasMany(UsersSocials::className(), ['user_id' => 'id']);
    }
}
