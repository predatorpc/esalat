<?php

namespace app\modules\basket\models;

use app\modules\catalog\models\CodesTypes;
use app\modules\common\models\User;
use app\modules\shop\models\Orders;
use app\modules\common\models\Zloradnij;
use Yii;

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
class PromoCode extends \app\modules\common\models\ActiveRecordRelation
{
    public $discount;
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
            [['type_id', 'user_id', 'count', 'status'], 'integer'],
            [['discount','date_begin', 'date_end'], 'safe'],
            [['code'], 'string', 'max' => 32],
            [['key'], 'string', 'max' => 4],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CodesTypes::className(), 'targetAttribute' => ['type_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Type ID',
            'user_id' => 'User ID',
            'code' => 'Code',
            'key' => 'Key',
            'count' => 'Count',
            'date_begin' => 'Date Begin',
            'date_end' => 'Date End',
            'status' => 'Status',
        ];
    }

    public static function generatePromocode($size = 4){
        $code = '';

        for ($i = 0; $i < $size ; $i++ ) {
            $code .= rand(0, 9);
        }

        $exists = self::find()->where('code = '.$code)->one();

        //Zloradnij::print_arr($code);die();

        if(empty($exists->code)) {
            return $code;
        }
        else {
            $code = self::generatePromocode();
            return $code;
        }

    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(CodesTypes::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['code_id' => 'id']);
    }

    public function calcCashBackByCode()
    {
        /*echo '<pre>';
        print_r($this->relatedRecords['type']->period_days);
        echo '</pre>';*/
        $result = Orders::find()->where(['orders.code_id'=>$this->id, 'orders.status'=>1])
            ->select('sum(orders_items.fee * orders_items.count) as current_cb')
            ->from('`orders`, orders_groups, orders_items')
            ->andWhere(['<=', 'orders.date', Yii::$app->params['promoPeriod'][$this->relatedRecords['type']->period_days]['last_dat']])
            ->andWhere(['>=', 'orders.date', Yii::$app->params['promoPeriod'][$this->relatedRecords['type']->period_days]['first_dat']])
            ->andWhere('orders_groups.order_id = orders.id')
            ->andWhere('orders_items.order_group_id = orders_groups.id')
            ->andWhere(['orders_items.status'=>1])
            ->asArray()->one();
        if(empty($result['current_cb'])){
            return 0;
        }
        else{
            return $result['current_cb'];
        }
    }
}
