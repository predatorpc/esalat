<?php

namespace app\modules\common\models;

use app\modules\managment\models\Shops;
use Yii;
use yii\db\Query;
use app\modules\common\models\UserRoles;

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
 * @property string $email
 * @property string $password
 * @property string $money
 * @property string $bonus
 * @property string $hash
 * @property integer $manager
 * @property integer $level
 * @property string $registration
 * @property integer $confirm
 * @property integer $status
 *
 * @property UsersCards[] $usersCards
 * @property UsersDrivers[] $usersDrivers
 * @property UsersPays[] $usersPays
 * @property UsersPins[] $usersPins
 * @property UsersRoles[] $usersRoles
 * @property UsersSocials[] $usersSocials
 */
class UserShop extends ActiveRecordRelation
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
            [['city_id', 'extremefitness', 's', 'manager', 'level', 'confirm', 'status'], 'integer'],
            [['birthday', 'registration'], 'safe'],
            [['money', 'bonus'], 'number'],
            [['name', 'email'], 'string', 'max' => 64],
            [['email'], 'safe'],
            [['phone'], 'string', 'max' => 16],
            [['password', 'hash'], 'string', 'max' => 32]
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
            'email' => 'Email',
            'password' => 'Password',
            'money' => 'Money',
            'bonus' => 'Bonus',
            'hash' => 'Hash',
            'manager' => 'Manager',
            'level' => 'Level',
            'registration' => 'Registration',
            'confirm' => 'Confirm',
            'status' => 'Status',
        ];
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
        return $this->hasMany(UserRoles::className(), ['user_id' => 'id']);
    }

    public function getShops()
    {
        return $this->hasMany(Shops::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersSocials()
    {
        return $this->hasMany(UsersSocials::className(), ['user_id' => 'id']);
    }

    public static function getIdentityUser(){
        //$_COOKIE['in_phone'] = '9137683413';
        //$_COOKIE['in_password'] = '1051991';
        if(isset($_COOKIE['in_phone']) && isset($_COOKIE['in_password'])){
            $searchPhone = trim($_COOKIE['in_phone']);
            $searchPass = md5('%'.trim($_COOKIE['in_password']).'%');

            $userShop = self::find()
                ->where(['phone' => $searchPhone])
                ->andWhere(['password' => $searchPass])
                ->andWhere(['status' => 1])
                ->one();

            if ($userShop){
                if(\Yii::$app->user->isGuest){
                    $currentUser = User::findByUsername($searchPhone);
                    Yii::$app->user->login($currentUser, 3600*24*30);
                }
                return $userShop->id;
            }

            $userShop = self::find()
                ->where(['phone' => $searchPhone])
                ->andWhere(['password' => $_COOKIE['in_password']])
                ->andWhere(['status' => 1])
                ->one();

            if ($userShop){
                if(\Yii::$app->user->isGuest){
                    $currentUser = User::findByUsername($searchPhone);
                    Yii::$app->user->login($currentUser, 3600*24*30);
                }
                return $userShop->id;
            }
        }

        return false;
    }

    public static function getIdentityShop(){
        $userId = self::getIdentityUser();
        if(!$userId){

        }else{
            $shop = UserRoles::find()
                ->where(['user_id' => $userId])
                ->andWhere(['status' => 1])
                ->one();
            if($shop){
                return $shop->shop_id;
            }
        }
        return false;
    }

    // Загрузка сохраненных банковских карт;
    public static function getUserCards(){
        $currentUser = \Yii::$app->user->identity;
        $userCards = (new Query())->from('users_cards')
            ->where(['user_id' => $currentUser->id])
            ->andWhere(['status' => 1])
            ->orderBy('id DESC')
            ->all();

        return $userCards;
    }

    public static function getUserPublicData(){
        $result = \Yii::$app->user;

        return $result;
    }

    public static function getUserExtremefitness(){
        $result = false;
        $currentUser = \Yii::$app->user->identity;
        if ($currentUser->extremefitness) {
            // Загрузка данных клиента WebFitness;
            $result = new Api();
            $result = $result->client_info($currentUser->extremefitness);
        }

        return $result;
    }
}