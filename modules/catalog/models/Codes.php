<?php

namespace app\modules\catalog\models;

use app\modules\common\models\ActiveRecordRelation;
use app\modules\common\models\Zloradnij;
use Yii;
use app\modules\common\models\User;
use app\modules\shop\models\Orders;

/**
 * This is the model class for table "codes".
 *
 * @property integer $id
 * @property integer $type_id
 * @property integer $user_id
 * @property string $code
 * @property string $key
 * @property integer $count
 * @property string $date_begin
 * @property string $date_end
 * @property integer $status
 *
 * @property CodesTypes $type
 * @property Users $user
 * @property Orders[] $orders
 */
class Codes extends ActiveRecordRelation
{
    public $summa;
    public $user_name;

    public $pay_user_id;
    public $code_owner;
    public $pay_money;
    public $order_id;
    public $code_id;
    public $name;
    public $phone;
    public $code_itself;
    public $countSale;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'codes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
         //   ['code','validateCode'],
            ['code','integer'],
            [['type_id', 'count', 'status', 'code'], 'required'],
            [['type_id', 'count', 'status'], 'integer'],
            [['date_begin', 'date_end','summa', 'user_id', 'user_name'], 'safe'],
            [['code'], 'string', 'max' => 32],
            [['key'], 'string', 'max' => 4]
        ];
    }

//    public function rules()
//    {
//        return [
//            [['type_id', 'count', 'status'], 'integer'],
//            [['date_begin', 'date_end','summa', 'user_id', 'user_name'], 'safe'],
//            [['code'], 'string', 'max' => 32],
//            [['key'], 'string', 'max' => 4]
//        ];
//    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Type ID',
            'user_id' => 'User ID',
            'code' => Yii::t('admin','Код'),
            'key' => 'Key',
            'count' => Yii::t('admin','Количество осталось'),
            'date_begin' => 'Date Begin',
            'date_end' => 'Date End',
            'status' => 'Status',
            'summa' => Yii::t('admin','Общая сумма заказов'),
        ];
    }

    public function validateCode(){
        $code = Codes::find()->where('code = \''.$this->code.'\'')->one();
        if (!empty($code)) {
            $this->addError('code', Yii::t('admin','Такой код уже есть'));
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(CodesTypes::className(), ['id' => 'type_id']);
    }
    public function getTypeName()
    {
        $type = $this->type;
        return $type ? $type->name : '';
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function getUserName()
    {
        $user = $this->user;

        return $user ? $user->name : '';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['code_id' => 'id']);
    }

    public function getDiscount()
    {
        return $this->type->discount;
    }

    public static function findByCode($id){

        return self::find()->where('code = '.$id)->one();
    }
}
