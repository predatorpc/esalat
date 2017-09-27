<?php
namespace app\modules\common\models;

use app\modules\basket\models\BasketLg;
use app\modules\catalog\models\Codes;
use app\modules\shop\models\Orders;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property datetime $registration
 * @property integer $updated_at
 * @property string $password write-only password
 * @property integer $city_id
 * @property integer $store_id
 *
 */

class User extends ActiveRecordRelation implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_FULL_DELETED = -1;
    const STATUS_ACTIVE = 1;
    public $city_id = 1001;
//    public $store_id = 0;
    public $discountPercent = 0;
    public $moneyCount = 0;
    public $moneySpend = 0;
    public $moneyDelivery = 0;
    public $code=0, $count=0;
    public $bonus_sum=0;
    public $orderDate;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED,self::STATUS_FULL_DELETED]],
            [['bonus','money','created_at', 'registration'],'safe'],
            ['secret_word','string', 'max' => 255],
            [['agree', ],'integer'],
//            ['phone', 'required', 'message' => 'Такой телефон уже зарегистрирован', 'when' => function($model) {
//                return !$model->findByPhone($model->phone);
//            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($name)
    {
        return static::findOne(['name' => $name, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByPhone($phone)
    {
        if(empty(static::findOne(['phone' => $phone, 'status' => self::STATUS_ACTIVE]))){
            return static::findOne(['phone' => '+7'.$phone, 'status' => self::STATUS_ACTIVE]);
        }
        else
            return static::findOne(['phone' => $phone, 'status' => self::STATUS_ACTIVE]);
    }


    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        $validatePassword = false;

        if(!isset($this->password_hash) || empty($this->password_hash)){

            if(md5('%'.$password.'%') == $this->password){
                $validatePassword = true;

                $this->password_hash = Yii::$app->security->generatePasswordHash($password);
                $this->auth_key = Yii::$app->security->generateRandomString();

                $allRoles = \Yii::$app->authManager->getRolesByUser($this->id);

                if(!isset($allRoles) || empty($allRoles)){
                    $auth = Yii::$app->authManager;
                    $userRole = $auth->getRole('user');
                    $auth->assign($userRole, $this->id);
                }

                if(!$this->save()){

                    //print_r($this->errors);die();
                };
            }
        }else{
            $validatePassword = Yii::$app->security->validatePassword($password, $this->password_hash);
        }

        return $validatePassword;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getDiscount(){
        return $this->basket ? $this->basket->promoCodePercent : 0;
    }

    public function setDiscountPercentValue($value){

    }

    public function getBasket(){
        return $this->hasOne(BasketLg::className(), ['user_id' => 'id'])->where(['basket.status' => 0]);
//        return (new BasketLg())->findCurrentBasket();
    }

    public function getOrders(){
        return $this->hasMany(Orders::className(), ['user_id' => 'id'])->where(['orders.status' => 1]);
    }

    public function getUsersPays(){
        return $this->hasMany(UsersPays::className(), ['id' => 'user_id']);
    }

    public function setDiscountPercent(){
        $this->discountPercent = $this->basket->promoCodePercent;
    }

    public function getCards(){
        return UsersCards::find()->where(['user_id' => $this->id])->all();
    }

    public function getPromoCodes(){
        return Codes::find()->where(['user_id' => $this->id])->all();
    }

}
