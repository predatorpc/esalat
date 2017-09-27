<?php
namespace app\modules\control\models;

use app\modules\catalog\models\Codes;
use app\modules\common\models\ActiveRecordRelation;
use app\modules\common\models\UsersBonus;
use app\modules\common\models\UsersPays;
use app\modules\shop\models\Orders;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

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

class UserQuery extends ActiveRecordRelation
{
    const STATUS_DELETED = 0;
    const STATUS_FULL_DELETED = -1;
    const STATUS_ACTIVE = 1;

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
        ];
    }

    public function getPromoCodeQuery(){
        return Codes::find()->where(['user_id' => $this->id]);
    }

    public function getOrdersQuery(){
        return Orders::find()->where(['user_id' => $this->id]);
    }

    public function getLastOrder(){
        return Orders::find()->where(['user_id' => $this->id])->orderBy('id DESC');
    }

    public function getSumUserPays(){
        return UsersPays::find()->where(['user_id' => $this->id,'status' => 1])->sum('money');
    }

    public function getUserPays(){
        return UsersPays::find()->where(['user_id' => $this->id,'status' => 1])->count();
    }

    public function getUserPaysBeforeOrder(Orders $order){
        return UsersPays::find()
            ->where([
                'user_id' => $this->id,
                'status' => 1,
            ])
            ->andWhere([
                '<', 'date', $order->date,
            ])
//            ->andWhere([
//                'OR',
//                ['<>','order_id',$order->id],
//                ['IS','order_id',NULL]
//            ])
            ->count();
    }

    public function getSumUserPaysBeforeOrder(Orders $order){
        return UsersPays::find()
            ->where(['user_id' => $this->id,'status' => 1])
            ->andWhere([
                '<', 'date', $order->date,
            ])
//            ->andWhere([
//                'OR',
//                ['<>','order_id',$order->id],
//                ['IS','order_id',NULL]
//            ])
            ->sum('money');
    }

    public function getSumUserBonusBeforeOrder(Orders $order){
        return UsersBonus::find()
            ->where(['user_id' => $this->id,'status' => 1])
            ->andWhere([
                '<', 'date', $order->date,
            ])
            ->sum('bonus');
    }
}
